<?php
class Faspaycc_BcaCc_Helper_Data extends Mage_Core_Helper_Abstract
{


	function _getTitle(){
		return Mage::getStoreConfig('payment/bcacc/title');
	}
	
	
	public function resp_faspay($act, $data, $specific="") {
		Mage::helper('bcacc')->createLogFaspaytbl();
		$sqllog	= "INSERT INTO order_payment_faspay_log(merchant_id, merchant_trx_id, trx_id, trx_status, trx_error_code, trx_error)
						values('".$data['MERCHANTID']."', ".$data['MERCHANT_TRANID'].", '".$data['TRANSACTIONID']."', '".$data['TXN_STATUS']."', '".$data['ERR_CODE']."','".$data['ERR_DESC']."')";
		
		switch($act) {
			case 'faspay_log':
				$sql = $sqllog;
				$connection	= Mage::getSingleton('core/resource')->getConnection('core_write');
				$update1	= $connection->query($sql);
				
				break;
			case 'faspay_report':
				$sql = $sqllog;
				$connection	= Mage::getSingleton('core/resource')->getConnection('core_write');
				$update1	= $connection->query($sql);
				
				$status		= "processing";
				if($specific=="pending") $status = "pending_payment";
				
				$sql = "update 	sales_flat_order a
						set		status = '$status'
						where	increment_id = '$data[MERCHANT_TRANID]'";
				$connection	= Mage::getSingleton('core/resource')->getConnection('core_write');		
				$update2	= $connection->query($sql);
				
				$sql = "update 	sales_flat_order_grid
						set		status = '$status' 
						where	increment_id = '$data[MERCHANT_TRANID]'";
				$connection	= Mage::getSingleton('core/resource')->getConnection('core_write');		
				$update3	= $connection->query($sql);
				
				break;
			case 'faspay_void':
				$status		= "canceled";
				
				$sql = "update 	sales_flat_order a
						set		status = '$status'
						where	increment_id = '$data[MERCHANT_TRANID]'";
				$connection	= Mage::getSingleton('core/resource')->getConnection('core_write');		
				$update2	= $connection->query($sql);
				
				$sql = "update 	sales_flat_order_grid
						set		status = '$status' 
						where	increment_id = '$data[MERCHANT_TRANID]'";
				$connection	= Mage::getSingleton('core/resource')->getConnection('core_write');		
				$update3	= $connection->query($sql);
				
				break;
			case 'faspay_error':
				$sql = $sqllog;
				if($specific!==""){
					$sql	= "INSERT INTO order_payment_faspay_log(merchant_id, merchant_trx_id, trx_id, trx_status, trx_error_code, trx_error)
								values('".$data['MERCHANTID']."', '".$data['MERCHANT_TRANID']."', '".$data['TRANSACTIONID']."', '".$data['TXN_STATUS']."', '".$data['ERR_CODE']."','".$specific."')";
				}
				$connection	= Mage::getSingleton('core/resource')->getConnection('core_write');
				$update1	= $connection->query($sql);
				
				break;
		}
	}
	public function createLogFaspaytbl() {
		$query = "
			CREATE TABLE IF NOT EXISTS `order_payment_faspay_log`  (
				`id` int(50) NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`merchant_id` varchar(100) DEFAULT NULL,
				`merchant_trx_id` int(50) DEFAULT NULL,
				`trx_id` int(50) DEFAULT NULL,
				`trx_status` varchar(32) DEFAULT NULL,
				`trx_error_code` varchar(32) DEFAULT NULL,
				`trx_error` varchar(100) DEFAULT NULL,
				`trx_timestamp` timestamp DEFAULT CURRENT_TIMESTAMP
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$rows = $connection->query($query);
		return true;
	}

	public function Cctypes($data){


		
		$cc=explode(',', $data);
		
		$new_array = array();
		foreach ($cc as $key => $value) {
			if ($value == 'VI') {
			$new_array[$value] = 'Visa';	
			}
			if ($value == 'MC') {
			$new_array[$value] = 'Mastercard';	
			}
			if ($value == 'JCB') {
			$new_array[$value] = 'JCB';	
			}
			if ($value == 'AM') {
			$new_array[$value] = 'Amex';	
			}
		}
		$cc_types=$new_array;
		return $cc_types;
	}

	public function inquiry($post){

		$server		= Mage::getStoreConfig('payment/bcacc/server_stage');
		
		$url 	= $server == 1 ? "https://ucdev.faspay.co.id/payment/PaymentInterface.jsp" : 
									"https://uc.faspay.co.id/payment/PaymentInterface.jsp";
		
		foreach($post as $key => $value){
			$post_items[] = $key . '=' . $value;
		}
		$postData = implode ('&', $post_items);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result	= curl_exec($ch);
		curl_close($ch);
		
		$lines	= explode(';',$result);
		$result = array();
		foreach($lines as $line){
			list($key,$value) = array_pad(explode('=', $line, 2), 2, null);
			$result[trim($key)] = trim($value);			
		}
		
		return $result;
	}
	
	public function void($post){
		$server		= Mage::getStoreConfig('payment/bcacc/server_stage');
		
		$url 	= $server == 1 ? "https://ucdev.faspay.co.id/payment/PaymentInterface.jsp" : 
									"https://uc.faspay.co.id/payment/PaymentInterface.jsp";
		
		foreach($post as $key => $value){
			$post_items[] = $key . '=' . $value;
		}
		$postData = implode ('&', $post_items);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result	= curl_exec($ch);
		curl_close($ch);
		
		$lines	= explode(';',$result);
		$result = array();
		foreach($lines as $line){
			list($key,$value) = array_pad(explode('=', $line, 2), 2, null);
			$result[trim($key)] = trim($value);			
		}
		
		return $result;
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