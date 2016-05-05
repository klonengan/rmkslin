<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    KS
 * @package     KS_KSDirectory
 * @copyright   Copyright (c) 2014 Kemana Services (http://www.kemanaservices.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * KS_KSDirectory_Block_Adminhtml_Directory_Edit_Tab_Regency
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Block_Adminhtml_Directory_Edit_Tab_Regency extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $_model = Mage::registry('ksdirectory_regency_data');
        $blockAction = strtolower(trim($this->getRequest()->getActionName()));

        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('ksdirectory_form', array('legend'=>Mage::helper('ksdirectory')->__('Regency')));

        $countryCode = '';
        if( $_model && $_model->getId())
        {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
                'value'     => $_model->getId()
            ));
            $countryCode = substr($_model->getRegencyCode(), 0, 2);
        }

        $country = $fieldset->addField('country_code', 'select', array(
            'label'     => Mage::helper('ksdirectory')->__('Country'),
            'class'     => 'required-entry',
            'required'  => true,
            'onchange'  => 'getprovince(this)',
            'name'      => 'country_code',
            'value'     => $countryCode,
            'values'    => Mage::getModel('ksdirectory/source_country')->toOptionArray(1),
            'tabindex'  => 1
        ));

        $country->setAfterElementHtml('
            <script>
            //<![CDATA[
                function getprovince(selectElement){
                    var reloadurl = "'. $this->getUrl("adminhtml/ksdirectory_directory/reloadprovince").'country/" + selectElement.value;
                    if(selectElement.value == ""){
                        $("province_code").update("");
                    }
                    else{
                        new Ajax.Request(reloadurl, {
                            method: "GET",
                            onLoading: function (stateform) {
                                $("province_code").update("<option value=\"\">Loading...</option>");
                            },
                            onComplete: function(stateform) {
                                $("province_code").update(stateform.responseText);
                            }
                        });
                    }
                    
                }
            //]]>
            </script>
        ');
        
        $fieldset->addField('province_code', 'select', array(
            'label'     => Mage::helper('ksdirectory')->__('Province'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'province_code',
            'value'     => $_model->getProvinceCode(),
            'values'    => Mage::getModel('ksdirectory/source_province')->toOptionArray(2, $countryCode),
            'tabindex'  => 2
        ));

        $fieldset->addField('regency_name', 'text', array(
            'label'     => Mage::helper('ksdirectory')->__('Regency Name'),
            'name'      => 'regency_name',
            'required'  => true,
            'value'     => $_model->getRegencyName(),
            'tabindex'  => 3
        ));

        $fieldset->addField('active', 'select', array(
            'label'     => Mage::helper('ksdirectory')->__('Status'),
            'name'      => 'active',
            'required'  => true,
            'value'     => $_model->getActive(),
            'values'    => array(
                                0 => Mage::helper('ksdirectory')->__('Disable'),
                                1 => Mage::helper('ksdirectory')->__('Enable')
                            ),
            'tabindex'  => 4
        ));
        
        return parent::_prepareForm();
    }
}
