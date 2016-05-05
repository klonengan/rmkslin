<?php
/**
 * Magento
 *
 * @author    Faspay http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
 */
 
class Faspay_BCAVA_Block_Direct extends Mage_Core_Block_Template {

    protected function _construct(){
		parent::_construct();
		echo $this->setTemplate('BCAVA/direct.phtml');
	}
	
	public function getContinueShoppingUrl(){
		return Mage::getUrl('checkout/cart');
	}
	
	public function getErrorMessage(){
		return Mage::getSingleton('checkout/session')->getFaspayccErrorMessage();
	}
}