<?php
/**
 * Magento
 *
 * @author    Faspay CC http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspaycc_BcaCc_Block_Processing extends Mage_Core_Block_Abstract
{
	/**
	 * prepare the HTML form and submit to FaspayCC server
	 */
	protected function _toHtml()
	{
		$payment	= $this->getOrder()->getPayment()->getMethodInstance();		
		//print_r($payment);exit;
        $form = new Varien_Data_Form();
        $form->setAction($payment->getUrl())
            ->setId('faspayredirect')
            ->setName('faspayredirect')
            ->setMethod('POST')
            ->setUseContainer(true);
        $submitButton = new Varien_Data_Form_Element_Submit(array(
            'value'    => $this->__('Klik disini jika dalam 5 detik belum diarahkan ke website pembayaran'),
        ));
        $form->addElement($submitButton);
		
		foreach ($payment->getFormFields() as $field=>$value) {
			$form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
		}
		
        $html = '<html><body>';
        $html.= $this->__('<p>Your payment information will be submitted for processing now, your browser will be redirect to the bank URL.</p>');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("faspayredirect").submit();</script>';
        $html.= '</body></html>';
		
        return $html;
	}
}
?>