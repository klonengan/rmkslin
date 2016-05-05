<?php
/**
 * Magento
 *
 * @author    Faspay http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_BCAKlikpay_Block_Form extends Mage_Payment_Block_Form
{
	protected function _getConfig()
	{
		return Mage::getSingleton('faspay/config');
	}
	
	protected function _construct()
	{
		$mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $mark->setTemplate('BcaKlikpay/form.phtml');
		
		$this->setTemplate('BCAKlikpay/formextend.phtml');
		
		
		return parent::_construct();
	}
}
?>