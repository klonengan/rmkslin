<?php
class Faspay_BcaKlik_Helper_Data extends Mage_Core_Helper_Abstract
{


	public function _getTitle() {

		return Mage::getStoreConfig('payment/klikbca/title');
	}
	
	
	public function _xml2array( $input, $callback = NULL, $_recurse = FALSE ) {
		$data = ( ( !$_recurse ) && is_string( $input ) ) ? simplexml_load_string( $input ) : $input;
		if ( $data instanceof SimpleXMLElement ) $data = (array) $data;
		if ( is_array( $data ) ) foreach ( $data as &$item ) $item = $this->_xml2array( $item, $callback, TRUE );
		return ( !is_array( $data ) && is_callable( $callback ) ) ? call_user_func( $callback, $data ) : $data;
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