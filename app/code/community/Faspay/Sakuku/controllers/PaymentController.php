<?php
/**
 * Magento
 *
 * @author    Faspay http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_Sakuku_PaymentController extends Mage_Core_Controller_Front_Action{
	protected $_redirectBlockType = 'Sakuku/processing';
	protected $_thanksBlockType	= 'Sakuku/thanks';

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
		
		$message	= Mage::helper('Sakuku')->__("Customer was redirected to Faspay.");
		$status		= Mage::getStoreConfig('payment/Sakuku/order_status');
		
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
			
			$order_status = Mage::helper('Sakuku')->__($msg);
			$order->addStatusHistoryComment($order_status);
			$this->norouteAction();
			return;
		}
		
		if ($this->getCheckout()->getFaspayRealOrderId() != $data['bill_no']) {
			$msg	= "Magento Transaction ID Not Match";
			
			$order_status = Mage::helper('Sakuku')->__($msg);
			$order->addStatusHistoryComment($order_status);
			$this->norouteAction();
			return;
		}
		
		$payment	= Mage::getSingleton('Sakuku/shared');
		$response	= $payment->inquiry_status($data);
		$dbstatus	= Mage::helper("Sakuku")->getOrderInfobyId($data["trx_id"]);
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
			
			$order_status = Mage::helper('Sakuku')->__('Faspay has processed the payment.');
			$order->addStatusHistoryComment($order_status);
		}else{
			$this->getCheckout()->setFaspayStatus("FAILED");
			
			$order->loadByIncrementId($data["bill_no"]);
			$msg	= "Payment Failed For Faspay Transaction ID : ".$data["trx_id"];
			$order_status = Mage::helper('Sakuku')->__($msg);
			$order->addStatusHistoryComment($order_status);
			
			$session = $this->getCheckout();
			$session->getQuote()->setIsActive(true)->save();
			
			$this->getCheckout()->setFaspayErrorMessage($msg);
		}
		
		$this->getCheckout()->setFaspayRedirectUrl(Mage::getUrl('*/*/thanks'));
		$order->save();
		
		return true;
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