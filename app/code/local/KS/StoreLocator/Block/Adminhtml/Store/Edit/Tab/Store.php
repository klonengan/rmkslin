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
 * @package     KS_StoreLocator
 * @copyright   Copyright (c) 2014 Kemana Services (http://www.kemanaservices.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * KS_StoreLocator_Block_Adminhtml_Store_Edit_Tab_Store
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_StoreLocator_Block_Adminhtml_Store_Edit_Tab_Store extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $_model = Mage::registry('storelocator_data');

        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('storelocator_form', array('legend'=>Mage::helper('storelocator')->__('General Information')));

        if( ! Mage::registry('storelocator_data')->getId())
        {
            $fieldset->addField('created_at', 'hidden', array(
                'name'      => 'created_at',
                'value'     => date('Y-m-d H:i:s')
            ));
            $fieldset->addField('updated_at', 'hidden', array(
                'name'      => 'updated_at',
                'value'     => date('Y-m-d H:i:s')
            ));
        }
        
        $province = $fieldset->addField('province', 'select', array(
            'label'     => Mage::helper('storelocator')->__('Province'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'province',
            'onchange'  => 'getcity(this)',
            'value'     => $_model->getProvince(),
            'values'    => Mage::getModel('storelocator/system_config_source_region')->toOptionArray(),
            'tabindex'  => 1
        ));

        $province->setAfterElementHtml('
            <script>
            //<![CDATA[
                function getcity(selectElement){
                    var reloadurl = "'. $this->getUrl("storelocator/adminhtml_store/reloadcity").'region/" + selectElement.value;
                    new Ajax.Request(reloadurl, {
                        method: "GET",
                        onLoading: function (stateform) {
                            $("city").update("<option value=\"\">Loading...</option>");
                        },
                        onComplete: function(stateform) {
                            $("city").update(stateform.responseText);
                        }
                    });
                }
            //]]>
            </script>
        ');

        $fieldset->addField('city', 'select', array(
            'label'     => Mage::helper('storelocator')->__('City'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'city',
            'value'     => $_model->getCity(),
            'values'    => Mage::getModel('storelocator/system_config_source_city')->toOptionArray($_model->getProvince()),
            'tabindex'  => 2
        ));

        $fieldset->addField('store_name', 'text', array(
            'label'     => Mage::helper('storelocator')->__('Store Name'),
            'name'      => 'store_name',
            'value'     => $_model->getStoreName(),
            'tabindex'  => 3
        ));

        $fieldset->addField('store_address1', 'text', array(
            'label'     => Mage::helper('storelocator')->__('Address Line 1'),
            'name'      => 'store_address1',
            'value'     => $_model->getStoreAddress1(),
            'tabindex'  => 4
        ));

        $fieldset->addField('store_address2', 'text', array(
            'label'     => Mage::helper('storelocator')->__('Address Line 2'),
            'name'      => 'store_address2',
            'value'     => $_model->getStoreAddress2(),
            'tabindex'  => 5
        ));

        $fieldset->addField('store_phone', 'text', array(
            'label'     => Mage::helper('storelocator')->__('Phone'),
            'name'      => 'store_phone',
            'value'     => $_model->getStorePhone(),
            'tabindex'  => 6
        ));

        $fieldset->addField('store_fax', 'text', array(
            'label'     => Mage::helper('storelocator')->__('Fax'),
            'name'      => 'store_fax',
            'value'     => $_model->getStoreFax(),
            'tabindex'  => 7
        ));

        $emailComment = "Use vertical bar &ldquo;<span style='font-weight:bold;color:#d71820'> | </span>&rdquo; to separate between emails";
        $fieldset->addField('store_email', 'text', array(
            'label'     => Mage::helper('storelocator')->__('Email'),
            'name'      => 'store_email',
            'value'     => $_model->getStoreEmail(),
            'after_element_html'   => $emailComment,
            'tabindex'  => 8
        ));

        $fieldset->addField('google_latitude', 'text', array(
            'label'     => Mage::helper('storelocator')->__('Google Map Latitude'),
            'name'      => 'google_latitude',
            'class'     => 'validate-number',
            'value'     => $_model->getGoogleLatitude(),
            'tabindex'  => 9
        ));

        $fieldset->addField('google_longitude', 'text', array(
            'label'     => Mage::helper('storelocator')->__('Google Map Longitude'),
            'name'      => 'google_longitude',
            'class'     => 'validate-number',
            'value'     => $_model->getGoogleLongitude(),
            'tabindex'  => 10
        ));

        $storeOperationComment = "Use vertical bar &ldquo;<span style='font-weight:bold;color:#d71820'> | </span>&rdquo; to separate between store operations";
        $fieldset->addField('store_operations', 'text', array(
            'label'     => Mage::helper('storelocator')->__('Store Operations'),
            'name'      => 'store_operations',
            'value'     => $_model->getStoreOperations(),
            'after_element_html'   => $storeOperationComment,
            'tabindex'  => 11
        )); 
        $fieldset->addField('is_hotspot', 'select', array(
            'label'     => Mage::helper('storelocator')->__('With Hot Spot Image'),
            'name'      => 'is_hotspot',
            'value'     => $_model['is_hotspot'],
            'values'    => array(
                                0 => Mage::helper('storelocator')->__('No'),
                                1 => Mage::helper('storelocator')->__('Yes')
                            ),
            'tabindex'  => 12
        ));
        
        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('storelocator')->__('Status'),
            'name'      => 'is_active',
            'value'     => $_model->getIsActive(),
            'values'    => array(
                                0 => Mage::helper('storelocator')->__('Disable'),
                                1 => Mage::helper('storelocator')->__('Enable')
                            ),
            'tabindex'  => 13
        ));
        return parent::_prepareForm();
    }
}
