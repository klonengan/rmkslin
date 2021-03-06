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
 * KS_KSDirectory_Block_Adminhtml_Directory_Edit_Tab_Village
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Block_Adminhtml_Directory_Edit_Tab_Village extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $_model = Mage::registry('ksdirectory_village_data');
        $blockAction = strtolower(trim($this->getRequest()->getActionName()));

        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('ksdirectory_form', array('legend'=>Mage::helper('ksdirectory')->__('Village')));

        $countryCode = '';
        $provinceCode = '';
        $regencyCode = '';
        if( $_model && $_model->getId())
        {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
                'value'     => $_model->getId()
            ));
            $countryCode = substr($_model->getSubdistrictCode(), 0, 2);
            $provinceCode = substr($_model->getSubdistrictCode(), 0, 5);
            $regencyCode = substr($_model->getSubdistrictCode(), 0, 8);
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
                        $("regency_code").update("");
                        $("subdistrict_code").update("");
                    }
                    else{
                        new Ajax.Request(reloadurl, {
                            method: "GET",
                            onLoading: function (stateform) {
                                $("province_code").update("<option value=\"\">Loading...</option>");
                                $("regency_code").update("");
                                $("subdistrict_code").update("");
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
        
        $province = $fieldset->addField('province_code', 'select', array(
            'label'     => Mage::helper('ksdirectory')->__('Province'),
            'class'     => 'required-entry',
            'required'  => true,
            'onchange'  => 'getregency(this)',
            'name'      => 'province_code',
            'value'     => $provinceCode,
            'values'    => Mage::getModel('ksdirectory/source_province')->toOptionArray(2, $countryCode),
            'tabindex'  => 2
        ));

        $province->setAfterElementHtml('
            <script>
            //<![CDATA[
                function getregency(selectElement){
                    var reloadurl = "'. $this->getUrl("adminhtml/ksdirectory_directory/reloadregency").'province/" + selectElement.value;
                    if(selectElement.value == ""){
                        $("regency_code").update("");
                        $("subdistrict_code").update("");
                    }
                    else{
                        new Ajax.Request(reloadurl, {
                            method: "GET",
                            onLoading: function (stateform) {
                                $("regency_code").update("<option value=\"\">Loading...</option>");
                                $("subdistrict_code").update("");
                            },
                            onComplete: function(stateform) {
                                $("regency_code").update(stateform.responseText);
                            }
                        });
                    }
                }
            //]]>
            </script>
        ');

        $regency = $fieldset->addField('regency_code', 'select', array(
            'label'     => Mage::helper('ksdirectory')->__('Regency'),
            'class'     => 'required-entry',
            'required'  => true,
            'onchange'  => 'getsubdistrict(this)',
            'name'      => 'regency_code',
            'value'     => $regencyCode,
            'values'    => Mage::getModel('ksdirectory/source_regency')->toOptionArray(2, $provinceCode),
            'tabindex'  => 3
        ));

        $regency->setAfterElementHtml('
            <script>
            //<![CDATA[
                function getsubdistrict(selectElement){
                    var reloadurl = "'. $this->getUrl("adminhtml/ksdirectory_directory/reloadsubdistrict").'regency/" + selectElement.value;
                    if(selectElement.value == ""){
                        $("subdistrict_code").update("");
                    }
                    else{
                        new Ajax.Request(reloadurl, {
                            method: "GET",
                            onLoading: function (stateform) {
                                $("subdistrict_code").update("<option value=\"\">Loading...</option>");
                            },
                            onComplete: function(stateform) {
                                $("subdistrict_code").update(stateform.responseText);
                            }
                        });
                    }
                }
            //]]>
            </script>
        ');

        $regency = $fieldset->addField('subdistrict_code', 'select', array(
            'label'     => Mage::helper('ksdirectory')->__('Sub-District'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'subdistrict_code',
            'value'     => $_model->getSubdistrictCode(),
            'values'    => Mage::getModel('ksdirectory/source_subdistrict')->toOptionArray(2, $regencyCode),
            'tabindex'  => 4
        ));

        $fieldset->addField('village_name', 'text', array(
            'label'     => Mage::helper('ksdirectory')->__('Village Name'),
            'name'      => 'village_name',
            'required'  => true,
            'value'     => $_model->getVillageName(),
            'tabindex'  => 5
        ));

        $fieldset->addField('postcode', 'text', array(
            'label'     => Mage::helper('ksdirectory')->__('Post Code'),
            'required'  => true,
            'name'      => 'postcode',
            'value'     => $_model->getPostcode(),
            'tabindex'  => 6
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
            'tabindex'  => 7
        ));
        
        return parent::_prepareForm();
    }
}
