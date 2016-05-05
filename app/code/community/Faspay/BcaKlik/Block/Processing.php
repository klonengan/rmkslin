<?php
/**
 * Magento
 *
 * @author    Faspay CC http://faspay.mediaindonusa.com <cs@mediaindonusa.com>
 * @copyright Copyright (C) 2013 MediaIndonusa. (http://faspay.mediaindonusa.com)
 *
**/

class Faspay_BcaKlik_Block_Processing extends Mage_Core_Block_Abstract
{
	/**
	 * prepare the HTML form and submit to FaspayCC server
	 */


    protected function _toHtml()
	{
		$payment  = $this->getOrder()->getPayment()->getMethodInstance();
        $form = new Varien_Data_Form();
        $url = Mage::getUrl('klikbca/payment/response', array('_secure' => false));

        $form->setAction($url)
            ->setId('faspayredirect')
            ->setName('faspayredirect')
            ->setMethod('POST')
            ->setUseContainer(true);
            $submitButton = new Varien_Data_Form_Element_Submit(array(
                'value'    => $this->__('Klik disini jika dalam 5 detik belum diarahkan ke website pembayaran'),
            ));
        $form->addElement($submitButton);

        //print_r($payment->getFormFields());die;

        foreach ($payment->getFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }

        $html = '<html><body>';
        $html.= $this->__('<p>Your Order will be submitted for processing now.</p>');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("faspayredirect").submit();</script>';
        $html.= '</body></html>';
        
        return $html;
   
	}


}
?>