<?php
/**
 * Magento
 *
 * @author    Faspay http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_BCAKlikpay_Block_Info extends Mage_Payment_Block_Info
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('BCAKlikpay/info.phtml');
	}
	
	public function getMethodCode()
	{
		return $this->getInfo()->getMethodInstance()->getCode();
	}

	public function toPdf()
	{
		$this->setTemplate('BCAKlikpay/pdf/info.phtml');
		return $this->toHtml();
	}
}
?>