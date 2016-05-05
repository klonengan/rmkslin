<?php
/**
 * Veritrans E-cash Mandiri Helper Data
 * @author Ryan Permana <rpermana@kemana.com>
 */
class KS_Ecashmandiri_Helper_Data extends Mage_Core_Helper_Abstract
{

	// Veritrans payment method title 
	function _getTitle(){
		return Mage::getStoreConfig('payment/ecashmandiri/title');
	}
	
	// progress side bar, if true then show logo image, vice versa
	function _getInfoTypeIsImage(){
		return Mage::getStoreConfig('payment/ecashmandiri/info_type');
	}
	
	// Message to be shown when Veritrans payment method is chosen
	function _getFormMessage(){
		return Mage::getStoreConfig('payment/ecashmandiri/form_message');
	}
}