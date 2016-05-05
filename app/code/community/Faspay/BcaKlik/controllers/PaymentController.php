<?php
/*
Mygateway Payment Controller
By: Junaid Bhura
www.junaidbhura.com
*/

class Faspay_BcaKlik_PaymentController extends Mage_Core_Controller_Front_Action {
	// The redirect action is triggered when someone places an order
	protected $_redirectBlockType = 'klikbca/processing';


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

	public function redirectAction() {
		$session = $this->getCheckout();
		$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($session->getLastRealOrderId());
		
		$order->save();
		$this->getResponse()->setBody(
			$this->getLayout()
				->createBlock($this->_redirectBlockType)
				->setOrder($order)
				->toHtml()
		);
		$session->unsQuoteId();
	}
		
	public function responseAction() {
		
		
		if ($this->getRequest()->get("status") == "00" && $this->getRequest()->get("trxno")) {

			$orderId = $this->getRequest()->get("trxno");
      		
      		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
      		
      		$message= "Order Was successfully receive by faspay";
      		
      		$order->addStatusHistoryComment($message);
      		
      		$order->save();
      		
      		Mage::getSingleton('checkout/session')->unsQuoteId();
      		
      		Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=> false));
    	}
    	
    	else {

    		$this->cancelAction();
      		Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
    	}
	}
		
	//http://yourdomain/index.php/klikbca/payment/notif
	public function notifAction() {

		$data = Mage::helper('klikbca')->_xml2array(urldecode(file_get_contents('php://input')));
		$merchant_code= Mage::getStoreConfig('payment/klikbca/merchantcode');

		$orderId = $data['trxno'];
      		
      	$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
      	$status= $order->getData('status');
		
      	if($status !='processing') {
			
			if ($merchant_code==$data['merchantcode']) {

	      		$invoice = $order->prepareInvoice()
	                  ->setTransactionId($order->getId())
	                  ->addComment('Payment successfully processed by faspay.')
	                  ->register()
	                  ->pay();

	                $transaction_save = Mage::getModel('core/resource_transaction')
	                  ->addObject($invoice)
	                  ->addObject($invoice->getOrder());

	                $transaction_save->save();
					
				$message = Mage::helper('klikbca')->__('Faspay has processed the payment.');
				$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING,true,$message);
				$order->save();

			$xml  = '<?xml version="1.0"?>' . "\n";
			$xml .= "<mi>" . "\n";
			$xml .= "<merchantcode>".$merchant_code."</merchantcode>" . "\n";
			$xml .= "<klikbcaid>".$data['klikbcaid']."</klikbcaid>" . "\n";
			$xml .= "<trxno>".$data['trxno']."</trxno>" . "\n";
			$xml .= "<trxdate>".$data['trxdate']."</trxdate>" . "\n";
			$xml .= "<status>00</status>" . "\n";
			$xml .= "<message>Sukses</message>" . "\n";
			$xml .= "</mi>" . "\n";

			echo $xml; 
	      		
			} else {

				echo "Failed validation";
			}
		} else {

			echo "Already Paid";
		}
	}

	public function cancelAction() {

    if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
        $order = Mage::getModel('sales/order')->loadByIncrementId(
            Mage::getSingleton('checkout/session')->getLastRealOrderId());
        
        if($order->getId()) {
      // Flag the order as 'cancelled' and save it
          $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED,
              true, 'Gateway has declined the payment.')->save();
        }
    }
  }
}