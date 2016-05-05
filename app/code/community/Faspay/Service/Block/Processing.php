<?php
/**
 * Magento
 *
 * @author    Faspay http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_Service_Block_Processing extends Mage_Core_Block_Abstract
{
	/**
	 * prepare the HTML form and submit to Faspay server
	 */
	protected function _toHtml()
	{
		$payment	= $this->getOrder()->getPayment()->getMethodInstance();
		$paychannel	= Mage::helper('Service')->getPaymentChannel();
		$formfield	= $payment->getFormFields();
		$redirect	= $payment->getUrl($paychannel,"redirect",$formfield);
		
		//Mage::helper("faspay")->dump($formfield,true);
        $form = new Varien_Data_Form();
        $form->setAction($redirect)
            ->setId('faspayredirect')
            ->setName('faspayredirect')
            ->setMethod('POST')
            ->setUseContainer(true);
        $submitButton = new Varien_Data_Form_Element_Submit(array(
            'value'    => $this->__('Klik disini jika dalam 5 detik belum diarahkan ke website pembayaran'),
        ));
        $form->addElement($submitButton);
		
		if(!empty($formfield)){
			foreach ($formfield as $field=>$value) {
				$form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
			}
		}

        $html = '<html><body>';
        $html.= $this->__('<p>Sekarang Anda akan diarahkan ke website pembayaran untuk melakukan pembayaran.</p>');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("faspayredirect").submit();</script>';
        $html.= '</body></html>';
		
        return $html;
	}
}
?>