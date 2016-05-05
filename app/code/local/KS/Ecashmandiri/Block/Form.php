<?php
/**
 * Veritrans E-cash Mandiri form block
 *
 * @category   Mage
 * @package    Mage_Veritrans_Mandiriclickpay_Block_Form
 * when Veritrans payment method is chosen, vtdirect/form.phtml template will be rendered through this class.
 *
 * @modified Ryan Permana <rpermana@kemana.com>
 */
class KS_Ecashmandiri_Block_Form extends Mage_Payment_Block_Form
{
    
    protected function _construct()
    {
        parent::_construct();
	    $this->setFormMessage(Mage::helper('ecashmandiri/data')->_getFormMessage());
        $this->setTemplate('ecashmandiri/form.phtml');
    }
}
?>