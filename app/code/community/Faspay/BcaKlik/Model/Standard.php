<?php
class Faspay_BcaKlik_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'klikbca';
	
	protected $_formBlockType = 'klikbca/form';
	protected $_infoBlockType = 'klikbca/info';

	protected $_isGateway				= true;
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	
	
	
	public function getOrder(){
		
		if (!$this->_order) {
			$paymentInfo = $this->getInfoInstance();
			$this->_order = Mage::getModel('sales/order')
							->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
		}
		return $this->_order;
	}

	public function getOrderPlaceRedirectUrl() {

		return Mage::getUrl('klikbca/payment/redirect', array('_secure' => true));
	}

	public function getRedirectBlockType(){
		return $this->_redirectBlockType;
	}

	public function getUrl(){
		$server		= Mage::getStoreConfig('payment/klikbca/server_stage');
		$rd = $server == 1 ?
			"http://klikbcadev.faspay.co.id:7703/receivetrxno.asp" :
			"http://klikbca.faspay.co.id:7703/receivetrxno.asp";
		
		return $rd;
	}

	public function getFormFields(){
		
		$response	= Mage::helper("klikbca")->_xml2array($this->post_data());
								
        return $response;
	}

	private function post_data() {

		$data 			= Mage::getSingleton('core/session')->getMydata();
		$klikbcaid		= $data['klikbcaid'];
		$lastOrderId 	= Mage::getSingleton('checkout/session')->getLastRealOrderId();		
		$order			= Mage::getModel('sales/order')->loadByIncrementID($lastOrderId);		
		$_totalData		= $order->getData();
		$amount			= number_format($_totalData['base_grand_total'],'0','.','');

		$merchant 		= Mage::getStoreConfig('payment/klikbca/merchant_name');
		$merchant_code	= Mage::getStoreConfig('payment/klikbca/merchantcode');
		$password		= Mage::getStoreConfig('payment/klikbca/password');
		$trxdate 		= date("d/m/Y H:i:s",strtotime($order['created_at']));
		$expired		= date("d/m/Y H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($order['created_at'])) . " +".Mage::getStoreConfig('payment/klikbca/order_expired')." hour"));

		$url 			= $this->getUrl();

		$str 			= "$merchant_code$password";
		$signature 		= md5($str);
		
		$xml  = '<?xml version="1.0"?>' . "\n";
		$xml .= "<mi>" . "\n";
		$xml .= "<perintah>receivetrxno</perintah>" . "\n";
		$xml .= "<merchantcode>".$merchant_code."</merchantcode>" . "\n";
		$xml .= "<klikbcaid>".$klikbcaid."</klikbcaid>" . "\n";
		$xml .= "<trxno>".$lastOrderId."</trxno>" . "\n";
		$xml .= "<trxdate>".$trxdate."</trxdate>" . "\n";
		$xml .= "<amount>".$amount."</amount>" . "\n";
		$xml .= "<description>Pembelian barang di ".$merchant."</description>" . "\n";
		$xml .= "<expiredate>".$expired."</expiredate>" . "\n";
		$xml .= "<sign>".$signature."</sign>" . "\n";
		$xml .= "</mi>" . "\n";


		$c = curl_init ($url);
		curl_setopt ($c, CURLOPT_POST, true);
		curl_setopt ($c, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt ($c, CURLOPT_POSTFIELDS, $xml);
		curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($c, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec ($c);
		curl_close ($c);

        //var_dump($response);die();
		return $response;
	}

	
}
?>