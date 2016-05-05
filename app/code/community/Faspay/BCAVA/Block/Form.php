<?php
/**
 * Magento
 *
 * @author    Faspay http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_BCAVA_Block_Form extends Mage_Payment_Block_Form
{
	protected function _getConfig()
	{
		return Mage::getSingleton('faspay/config');
	}
	
	protected function _construct()
	{
		$mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $mark->setTemplate('BCAVA/form.phtml');
		
		$this->setTemplate('BCAVA/formextend.phtml');
		
		
		return parent::_construct();
	}
}
?>