<?php
/**
 * Magento
 *
 * @author    Faspay http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_BCAKlikpay_Helper_Data extends Mage_Payment_Helper_Data
{
	//Get total price
	public function getTotalPrice() {
		$session = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order = Mage::getModel('sales/order')->loadByIncrementId($session);
		$baseCode = Mage::app()->getBaseCurrencyCode();
		$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
		$rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCode, array_values($allowedCurrencies));
		$totalPrice = $order->getBaseGrandTotal();
		return $totalPrice;
	}

	public function getProducts() {
		$session = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order = Mage::getModel('sales/order')->loadByIncrementId($session);
		$items = $order->getAllVisibleItems();

		$products = "";

		foreach ($items as $item) {
			$products .= $item->getName() . " ";
			$products .= "Qty: ".$item->getQtyToInvoice();
			$products .= ", ";
		}

		return $products;
	}

	public function getUserName() {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return trim($customer->getName());
    }

    public function getUserEmail()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getEmail();
    }
	
	public function getPaymentChannel($id=null,$data=null){
		if($id==null){
			$session	= Mage::getSingleton('checkout/session');
			$quote_id	= $session->getQuoteId();
			$quote		= Mage::getModel('sales/quote')->load($quote_id );
			$paymentcode= $quote->getPayment()->getMethodInstance()->getCode();			
		}else{
			$order	= Mage::getModel('sales/order')->loadByIncrementId($data["bill_no"]);
		}
		return $paymentcode;
	}
	
	
	public function _bcakp_signature(){
		$user_id	= Mage::getStoreConfig('payment/BCAKlikpay/merchant_id');
		$password	= Mage::getStoreConfig('payment/BCAKlikpay/merchant_name');
		$orderId	= Mage::getSingleton('checkout/session')->getLastRealOrderId();
		return sha1(md5($user_id.$password.$orderId));
	}
	
	public function _bcakp_signature_ori($billno) {
		$sig	= '';
		$rsp	= $this->getOrderInfobyId($billno);
		
		if(count($rsp)) {
			$keyId		= $this->_bcakp_keyId();
			$klikPayCode= Mage::getStoreConfig('payment/BCAKlikpay/klikpaycode');

			$currency	= 'IDR';
			$tempKey1	= $klikPayCode . $billno . $currency . $keyId;
			$hashKey1	= $this->_getHash($tempKey1);
			$transactionDate	= $rsp['updated_at'];
			$amount				= $rsp['subtotal']+$rsp['discount_amount'];
			$expDate			= date('dmY',strtotime ($transactionDate));
			$strDate    		= $this->_intval32bits($expDate); 
			$amt 				= $this->_intval32bits((int)$amount);
			$tempKey2 			= $strDate + $amt;
			$hashKey2 			= $this->_getHash((string)$tempKey2);
			$sig 				= abs($hashKey1 + $hashKey2);
			
		}
		

		return $sig;
	
	}
	
	public function getOrderInfobyId($trx_id){
		$sql	= "select a.increment_id, b.payment_channel, a.subtotal, a.status, a.discount_amount, a.updated_at
					from sales_flat_order a, order_payment_faspay b
					where a.increment_id = b.order_id and b.trx_id = '$trx_id'";
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rsp = $connection->fetchAll($sql);
		
		return $rsp[0];
	}
	
	public function _getHash($value) {
		$h = 0;
		for ($i = 0;$i < strlen($value);$i++) {
			$h = $this->_intval32bits($this->_add31T($h) + ord($value{$i}));
		}
		return $h;
	}
	
	public function _xml2array( $input, $callback = NULL, $_recurse = FALSE ) {
		$data = ( ( !$_recurse ) && is_string( $input ) ) ? simplexml_load_string( $input ) : $input;
		if ( $data instanceof SimpleXMLElement ) $data = (array) $data;
		if ( is_array( $data ) ) foreach ( $data as &$item ) $item = $this->_xml2array( $item, $callback, TRUE );
		return ( !is_array( $data ) && is_callable( $callback ) ) ? call_user_func( $callback, $data ) : $data;
	}
	
	public function _decrypt($encrypted) {
		$key = hash('SHA256', '!kQm*fF3pXe1Kbm%9' . '*!nD0n3s5!4#', true);
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
		$encrypted = substr($encrypted, 22);
		$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
		$hash = substr($decrypted, -32);
		$decrypted = substr($decrypted, 0, -32);
		if (md5($decrypted) != $hash) return false;
		return $decrypted;
	}
	
	public function resp_faspay($act, $data) {
		$this->createPaymentFaspaytbl();
		switch($act) {
			case 'post_data':
				if($data['response_code']=='00') {
					$sql = "INSERT INTO order_payment_faspay(order_id, trx_id, payment_channel, payment_status)
								values(".$data['bill_no'].", '".$data['trx_id']."','".$data['payment_channel']."','".$data['status']."')";
					$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
					$hasil = $connection->query($sql);
					
					return $hasil;
				}
				break;
			case 'faspay_report':			
				if(isset($data["trx_id"]) && isset($data["payment_status_code"]) && isset($data["payment_date"]) && isset($data["payment_reff"]) && isset($data["payment_status_desc"])){
					$reff	= implode("#",$data['payment_reff']);
					$sql = "update 	order_payment_faspay set
									payment_status = '$data[payment_status_desc]',
									payment_date = '$data[payment_date]',
									payment_reff='$reff'
							where 	trx_id = '$data[trx_id]'";
					$connection=Mage::getSingleton('core/resource')->getConnection('core_write');
					$update1=$connection->query($sql);
				}
				break;
		}
	}
	
	public function createPaymentFaspaytbl() {
		$query = "CREATE TABLE IF NOT EXISTS `order_payment_faspay` (
					`order_id` int(11) NOT NULL,
					`trx_id` varchar(32) NOT NULL,
					`payment_channel` varchar(32) DEFAULT NULL,
					`payment_status` varchar(32) DEFAULT NULL,
					`payment_date` timestamp NULL DEFAULT NULL,
					`payment_reff` varchar(32) DEFAULT NULL,
					`auth_code` varchar(32) DEFAULT NULL,
					PRIMARY KEY (`order_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rows = $connection->query($query);
		return true;
	}
	
	public function redirectPageFaspay($url){
		return $this->_redirectUrl($url);
	}
	
	public function hex2bin($hexSource) {	
		$bin = '';
		$strlen = strlen($hexSource);
		for ($i=0;$i<strlen($hexSource);$i=$i+2) {
			$bin .= chr(hexdec(substr($hexSource,$i,2)));
		}
		return $bin;
	}

	public function _intval32bits($value) {
        if ($value > 2147483647)
            $value = ($value - 4294967296);
		else if ($value < -2147483648)
            $value = ($value + 4294967296);
        return $value;
    }
	
	public function _add31T($value) {
		$result = 0;
		for($i=1;$i <= 31;$i++) {
			$result = $this->_intval32bits($result + $value);
		}
		return $result;
	}
	
	public function _bcakp_keyId() {
		$clearKey = Mage::getStoreConfig('payment/BCAKlikpay/clearkey');
		return strtoupper(bin2hex(pack("a" . strlen($clearKey), $clearKey)));
	}
	
	public function dump($arg, $die=true) {
		if (is_string($arg) && preg_match("/xml/i", $arg)) {
			echo header("Content-type: application/xml");
			echo $arg;
		}
		else {
			echo "<br /><pre>";
			if(is_string($arg)) echo $arg;
			else print_r($arg);
			echo "</pre><br />";
		}
		if($die) die();
	}
}
?>