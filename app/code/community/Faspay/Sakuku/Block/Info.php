<?php
/**
 * Magento
 *
 * @author    Faspay http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_Sakuku_Block_Info extends Mage_Payment_Block_Info
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('Sakuku/info.phtml');
	}
	
	public function getMethodCode()
	{
		return $this->getInfo()->getMethodInstance()->getCode();
	}

	public function toPdf()
	{
		$this->setTemplate('Sakuku/pdf/info.phtml');
		return $this->toHtml();
	}
}
?>