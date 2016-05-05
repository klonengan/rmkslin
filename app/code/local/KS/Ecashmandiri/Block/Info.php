<?php
/**
 * Veritrans E-cash Mandiri form block
 * @modified Ryan Permana <rpermana@kemana.com>
 */
class KS_Ecashmandiri_Block_Info extends Mage_Payment_Block_Info
{
    
    protected function _construct()
    {
        parent::_construct();
	$this->setInfoMessage( Mage::helper('ecashmandiri/data')->_getInfoTypeIsImage() == true ?
		'<img src="'. $this->getSkinUrl('images/Veritrans.png'). '"/>' : '<b>'. Mage::helper('ecashmandiri/data')->_getTitle() . '</b>');
	$this->setPaymentMethodTitle( Mage::helper('ecashmandiri/data')->_getTitle() );
        $this->setTemplate('ecashmandiri/info.phtml');
    }
}
?>
