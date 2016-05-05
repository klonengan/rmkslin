<?php
/**
 * Magento
 *
 * @author    Faspay CC http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_BcaKlik_Block_Info extends Mage_Payment_Block_Info
{
	
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('klikbca/info.phtml');
	}
	
	public function getMethodCode()
	{
		return $this->getInfo()->getMethodInstance()->getCode();
	}

	
}
?>