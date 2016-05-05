<?php
/**
 * Magento
 *
 * @author    Faspay http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_BCAKlikpay_PaymentController extends Mage_Core_Controller_Front_Action{
	protected $_redirectBlockType = 'BCAKlikpay/processing';
	protected $_thanksBlockType	= 'BCAKlikpay/thanks';

	protected function _expireAjax(){
		if (!$this->getCheckout()->getQuote()->hasItems()) {
			$this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
			exit;
		}
	}
	
    public function formAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function getCheckout(){
		return Mage::getSingleton('checkout/session');
	}

	public function redirectAction(){
		$session	= $this->getCheckout();
		$session->setFaspayQuoteId($session->getQuoteId());
		$session->setFaspayRealOrderId($session->getLastRealOrderId());
		
		$order		= Mage::getModel('sales/order');
		$order->loadByIncrementId($session->getLastRealOrderId());
		
		$message	= Mage::helper('BCAKlikpay')->__("Customer was redirected to Faspay.");
		$status		= Mage::getStoreConfig('payment/BCAKlikpay/order_status');
		
		if($status=="pending"){
			$order->addStatusHistoryComment($message);
		}elseif($status=="processing"){
			$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, $message);
		}elseif($status=="complete"){
			$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_COMPLETE, $message);
		}elseif($status=="closed"){
			$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CLOSED, $message);
		}elseif($status=="canceled"){
			$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, $message);
		}elseif($status=="holded"){
			$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_HOLDED, $message);
		}else{
			$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $message);
		}
		
		$order->save();

		$this->getResponse()->setBody(
			$this->getLayout()
				->createBlock($this->_redirectBlockType)
				->setOrder($order)
				->toHtml()
		);

		$session->unsQuoteId();
	}
	
	public function thanksAction() {
		$status = $this->processCallback();
		
		if ($status) {
			$this->loadLayout();
			$this->renderLayout();
			
			$status	= Mage::getSingleton('checkout/session')->getFaspayStatus();
			$trxid	= Mage::getSingleton('checkout/session')->getFaspayTrxId();
			
			$this->getResponse()->setBody(
				$this->getLayout()
					->createBlock($this->_thanksBlockType)
					->setData('faspay_status', $status)
					->setData('faspay_trxid', $trxid)
					->toHtml()
			);
		}
	}
	
	protected function processCallback(){
		if (!$this->getRequest()->getParams()) {
			$this->norouteAction();
			return;
		}
		
		$data = $this->getRequest()->getParams();
		//Mage::helper('faspay')->dump($data,true);
		if (!isset($data['trx_id']) || !isset($data['bill_no'])) {
			$this->norouteAction();
			return;
		}
		
		$order		= Mage::getModel('sales/order')->loadByIncrementId($data["bill_no"]);
		if(!$order->getId()){
			$msg	= "Magento Transaction ID Empty";
			
			$order_status = Mage::helper('BCAKlikpay')->__($msg);
			$order->addStatusHistoryComment($order_status);
			$this->norouteAction();
			return;
		}
		
		if ($this->getCheckout()->getFaspayRealOrderId() != $data['bill_no']) {
			$msg	= "Magento Transaction ID Not Match";
			
			$order_status = Mage::helper('BCAKlikpay')->__($msg);
			$order->addStatusHistoryComment($order_status);
			$this->norouteAction();
			return;
		}
		
		$payment	= Mage::getSingleton('BCAKlikpay/shared');
		$response	= $payment->inquiry_status($data);
		$dbstatus	= Mage::helper("BCAKlikpay")->getOrderInfobyId($data["trx_id"]);
		//Mage::helper('faspay')->dump($response,true);
		
		$this->getCheckout()->setFaspayTrxId($data["trx_id"]);
		$status	= isset($data["status"]) ? $data["status"] : "0";
		
		if( $response=="2" || $status=="2" || $dbstatus["status"]=="processing"){
			$this->getCheckout()->setFaspayStatus("SUCCESS");
			$this->getCheckout()->setLastTransId($data["trx_id"]);
				
			$session = $this->getCheckout();
			$session->unsFaspayRealOrderId();
			$session->setFaspayId($session->getFaspayQuoteId(true));
			$session->getQuote()->setIsActive(false)->save();
			
			$invoice = $order->prepareInvoice();
			$invoice->register()->capture();
			Mage::getModel('core/resource_transaction')
				->addObject($invoice)
				->addObject($invoice->getOrder())
				->save();
			
			$order_status = Mage::helper('BCAKlikpay')->__('Faspay has processed the payment.');
			$order->addStatusHistoryComment($order_status);
		}else{
			$this->getCheckout()->setFaspayStatus("FAILED");
			
			$order->loadByIncrementId($data["bill_no"]);
			$msg	= "Payment Failed For Faspay Transaction ID : ".$data["trx_id"];
			$order_status = Mage::helper('BCAKlikpay')->__($msg);
			$order->addStatusHistoryComment($order_status);
			
			$session = $this->getCheckout();
			$session->getQuote()->setIsActive(true)->save();
			
			$this->getCheckout()->setFaspayErrorMessage($msg);
		}
		
		$this->getCheckout()->setFaspayRedirectUrl(Mage::getUrl('*/*/thanks'));
		$order->save();
		
		return true;
	}
	
	public function checkBcakpSignatureAction(){ //http://localhost/magento/index.php/faspay/payment/checkbcakpsignature?trx_id=&signature=
		$ack	= 0;
		
		$payment		= Mage::getSingleton('BCAKlikpay/shared');
		$transactionNo 	= $this->getRequest()->getParam('trx_id');
		$signature		= $this->getRequest()->getParam('signature');
		$authKey		= $this->getRequest()->getParam('authkey');
		
		if($transactionNo && $signature) {
			$rsp	= $payment->check_bcakpsignature($transactionNo);
			if(count($rsp)) {
				$keyId				= Mage::helper('BCAKlikpay')->_bcakp_keyId();
				$klikPayCode		= Mage::getStoreConfig('payment/BCAKlikpay/klikpaycode');
				$currency			= 'IDR';
				$tempKey1			= $klikPayCode . $transactionNo . $currency . $keyId;
				
				$hashKey1			= Mage::helper('BCAKlikpay')->_getHash($tempKey1);
				
				$transactionDate 	= $rsp[0]['created_at'];
				$amount				= $rsp[0]['subtotal']+$rsp[0]['discount_amount'];
				
				$expDate			= date('dmY',strtotime ($transactionDate));
				$strDate 			= Mage::helper('BCAKlikpay')->_intval32bits($expDate);
				$amt 				= Mage::helper('BCAKlikpay')->_intval32bits((int)$amount);
				$tempKey2 			= $strDate + $amt;
				$hashKey2 			= Mage::helper('BCAKlikpay')->_getHash((string)$tempKey2);
				
				$sig 				= abs($hashKey1 + $hashKey2);
				$ack 				= $sig == $signature ? 1 : 0;
			
			}
		}else if($transactionNo && $authKey) {
			$rsp	= $payment->check_bcakpsignature($transactionNo);
			
			if(count($rsp)) {
				$keyId	  			= Mage::helper('BCAKlikpay')->_bcakp_keyId();
				$klikPayCode		= Mage::getStoreConfig('payment/BCAKlikpay/klikpaycode');
				$currency			= 'IDR';
				$transactionDate	= $rsp[0]['created_at'];
				$transactionDate	= date('d/m/Y H:i:s', strtotime($transactionDate));
				$klikPayCode 		= str_pad($klikPayCode, 10, "0");
				$transactionNo 		= str_pad($transactionNo, 18, "A");
				$currency 			= str_pad($currency, 5, "1");
				$value_1 			= $klikPayCode . $transactionNo . $currency . $transactionDate . $keyId;
				$hashv_1 			= strtoupper(md5($value_1));

				if (strlen($keyId) == 32)
					$key = $keyId . substr($keyId,0,16);
				else if (strlen($keyId) == 48)
					$key = $keyId;	
				
				$aKey = strtoupper(bin2hex(mcrypt_encrypt(MCRYPT_3DES, pack("H" . strlen($key), $key), pack("H" . strlen($hashv_1), $hashv_1), MCRYPT_MODE_ECB)));
				$ack  = $aKey==$authKey ? 1 : 0;
			}
		
	   }
	   echo $ack;
	}

	public function cancelAction() {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if($order->getId()) {
				$order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Faspay has declined the payment.')->save();
			}
        }
	}
}