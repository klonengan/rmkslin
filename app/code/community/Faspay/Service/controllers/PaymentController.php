<?php
/**
 * Magento
 *
 * @author    Faspay http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_Service_PaymentController extends Mage_Core_Controller_Front_Action{
	protected $_redirectBlockType = 'Service/processing';
	protected $_thanksBlockType	= 'Service/thanks';

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
		
		$message	= Mage::helper('Service')->__("Customer was redirected to Faspay.");
		$status		= Mage::getStoreConfig('Service/settings/order_status');
		
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
			
			$order_status = Mage::helper('Service')->__($msg);
			$order->addStatusHistoryComment($order_status);
			$this->norouteAction();
			return;
		}
		
		if ($this->getCheckout()->getFaspayRealOrderId() != $data['bill_no']) {
			$msg	= "Magento Transaction ID Not Match";
			
			$order_status = Mage::helper('Service')->__($msg);
			$order->addStatusHistoryComment($order_status);
			$this->norouteAction();
			return;
		}
		
		$payment	= Mage::getSingleton('Service/shared');
		$response	= $payment->inquiry_status($data);
		$dbstatus	= Mage::helper("Service")->getOrderInfobyId($data["trx_id"]);
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
			
			$order_status = Mage::helper('Service')->__('Faspay has processed the payment.');
			$order->addStatusHistoryComment($order_status);
		}else{
			$this->getCheckout()->setFaspayStatus("FAILED");
			
			$order->loadByIncrementId($data["bill_no"]);
			$msg	= "Payment Failed For Faspay Transaction ID : ".$data["trx_id"];
			$order_status = Mage::helper('Service')->__($msg);
			$order->addStatusHistoryComment($order_status);
			
			$session = $this->getCheckout();
			$session->getQuote()->setIsActive(true)->save();
			
			$this->getCheckout()->setFaspayErrorMessage($msg);
		}
		
		$this->getCheckout()->setFaspayRedirectUrl(Mage::getUrl('*/*/thanks'));
		$order->save();
		
		return true;
	}
	
	public function notifyAction() {
		if(isset($_POST) && ($_SERVER["REMOTE_ADDR"]=="202.153.31.82" || $_SERVER["REMOTE_ADDR"]=="202.153.30.122" || $_SERVER["REMOTE_ADDR"]=="117.104.201.30" || $_SERVER["REMOTE_ADDR"]=="117.104.201.29")){
			$data = Mage::helper('Service')->_xml2array(urldecode(file_get_contents('php://input')));
			
			$ssignature		= $data["signature"];
			$payment_status = $data["payment_status_code"];
			
			$merchantus		= Mage::getStoreConfig('Service/settings/merchant_id');
			$password		= Mage::getStoreConfig('Service/settings/merchant_password');
			$lastOrderId	= Mage::helper("Service")->getOrderInfobyId($data["trx_id"]);
			
			$mstr 			= $merchantus.$password.$lastOrderId["increment_id"].$payment_status;
			$msignature		= sha1(md5($mstr));
			
			if($ssignature!==$msignature){
				echo "You Are Not Allowed To Access This Page !";
				exit;
			}
			
			if(isset($data["trx_id"]) && isset($data["payment_status_code"]) && isset($data["payment_date"]) && isset($data["payment_reff"]) && isset($data["payment_status_desc"])){
				Mage::helper('Service')->resp_faspay('faspay_report', $data);
				$order	= Mage::getModel('sales/order')->loadByIncrementId($data["bill_no"]);
				
				if($data["payment_status_code"]=="2"){
					$order_status = Mage::helper('Service')->__('Faspay has notified Merchant, Success Transaction with ID : '.$data["trx_id"]);
					$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, $order_status);
				}elseif($data["payment_status_code"]=="3" || $data["payment_status_code"]=="4" || $data["payment_status_code"]=="8"){
					$order_status = Mage::helper('Service')->__('Faspay has notified Merchant, Failed or Reversal or Canceled Transaction with ID : '.$data["trx_id"]);
					$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $order_status);
				}elseif($data["payment_status_code"]=="4"){
					$order_status = Mage::helper('Service')->__('Faspay has notified Merchant, Payment Reversal with ID : '.$data["trx_id"]);
					$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $order_status);
				}else{
					$order_status = Mage::helper('Service')->__('Faspay has notified Merchant, Unknown Transaction with ID : '.$data["trx_id"]);
					$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $order_status);
				}
				$order->save();
			}

			$xml  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			$xml .= "<faspay>" . "\n";
			$xml .= "<request>Payment Notification</request>" . "\n";
			$xml .= "<trx_id>$data[trx_id]</trx_id>" . "\n";
			$xml .= "<merchant_id>$data[merchant_id]</merchant_id>" . "\n";
			$xml .= "<bill_no>$data[bill_no]</bill_no>" . "\n";
			$xml .= "<response_code>00</response_code>" . "\n";
			$xml .= "<response_desc>Sukses</response_desc>" . "\n";
			$xml .= "<response_date>".date("Y-m-d H:i:s")."</response_date>" . "\n";
			$xml .= "</faspay>" . "\n";
			echo header("Content-type: application/xml");
			echo $xml;
		}else{
			echo "You Are Not Allowed To Access This Page !";
		}
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