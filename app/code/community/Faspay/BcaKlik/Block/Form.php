<?php
/**
 * Magento
 *
 * @author    Faspay CC http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_BcaKlik_Block_Form extends Mage_Payment_Block_Form
{
	protected function _construct() {
        
        parent::_construct();
        
        $this->setTemplate('klikbca/form.phtml');     
    }
     
}
?>