<?php
/**
 * Veritrans E-cash Mandiri Model Standard
 * @modified Ryan Permana <rpermana@kemana.com>
 */
class KS_Ecashmandiri_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	
    protected $_code = 'ecashmandiri';
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	
	protected $_formBlockType = 'ecashmandiri/form';
    protected $_infoBlockType = 'ecashmandiri/info';

	// call to redirectAction function at Veritrans_CIMB Clicks_PaymentController
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('ecashmandiri/payment/redirect', array('_secure' => true));
	}

}
?>