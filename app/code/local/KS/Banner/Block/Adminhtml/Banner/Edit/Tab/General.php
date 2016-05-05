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
 * @package     KS_Banner
 * @copyright   Copyright (c) 2014 Kemana Services (http://www.kemanaservices.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * KS_Banner_Block_Adminhtml_Banner_Edit_Tab_General
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_Banner_Block_Adminhtml_Banner_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    // protected function _toHtml()
    // {
    //     $dependencyBlock = $this->getLayout()
    //         ->createBlock('adminhtml/widget_form_element_dependence')
    //             ->addFieldMap('type_id', 'type_id')
    //             ->addFieldMap('imagebanner', 'imagebanner')
    //             ->addFieldMap('target_url', 'target_url')
    //             ->addFieldMap('sort_order', 'sort_order')
    //             ->addFieldMap('active', 'active')
    //             ->addFieldDependence(array('imagebanner', 'target_url', 'sort_order', 'active'), 'type_id', array(1,2));
    //             // ->addFieldDependence('target_url', 'type_id', '1,2')
    //             // ->addFieldDependence('sort_order', 'type_id', '1,2')
    //             // ->addFieldDependence('active', 'type_id', '1,2');

    //    return parent::_toHtml() . $dependencyBlock->toHtml();
    // }

    protected function _prepareForm()
    {
        $data = Mage::registry('banner_list_data');
        $urlMedia = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'wysiwyg/banner/';

        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('banner_form', array('legend'=>Mage::helper('banner')->__('General')));
        
        $typeId = '';
        $image = '';
        $sortOrder = 0;
        $active = 1;
        $targetUrl = '';
        $width = '';
        $height = '';
        $note = '...';
        if(array_key_exists(0, $data) && count($data) > 0)
        {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
                'value'     => $data[0]['id']
            ));
            
            $typeId = $data[0]['type_id'];
            $image = $data[0]['image'] ? $urlMedia.str_pad($typeId, 3, '0', STR_PAD_LEFT).'/'.$data[0]['image'] : '';
            $targetUrl = $data[0]['target_url'];
            $sortOrder = $data[0]['sort_order'];
            $categoryId = $data[0]['category_id'];
            $active = $data[0]['active'];
            $width = $data[0]['width'];
            $height = $data[0]['height'];
            $note = 'The resolution of this image must be exactly '.$width.' x '.$height.' (W x H)';
        }

        $bannerType = $fieldset->addField('type_id', 'select', array(
            'label'     => Mage::helper('banner')->__('Banner Type'),
            'class'     => 'required-entry',
            'required'  => true,
            'onchange'  => 'updateresolution(this)',
            'name'      => 'type_id',
            'value'     => $typeId,
            'values'    => Mage::getModel('banner/source_type')->toOptionArray(true),
            'tabindex'  => 1
        ));

        $bannerType->setAfterElementHtml('
            <script>
            //<![CDATA[
                function updateresolution(selectElement){
                    var reloadurl = "'. $this->getUrl("adminhtml/banner_banner/updateresolution").'type/" + selectElement.value;
                    if(selectElement.value == ""){
                        $("note_imagebanner").update("");
                    }
                    else{
                        new Ajax.Request(reloadurl, {
                            method: "GET",
                            onLoading: function (stateform) {
                                $("note_imagebanner").update("<option value=\"\">Please wait...</option>");
                            },
                            onComplete: function(stateform) {
                                $("note_imagebanner").update(stateform.responseText);
                            }
                        });
                    }
                    
                }
            //]]>
            </script>
        ');

        $fieldset->addField('category_id', 'select', array(
            'label'     => Mage::helper('banner')->__('Select Category'),
            'class'     => 'required-entry',
            'values'    => Mage::getModel('banner/list')->getCategory(),
            'name'      => 'category_id',
        ));

        $fieldset->addField('imagebanner', 'image', array(
            'label'     => Mage::helper('banner')->__('Image'),
            'name'      => 'imagebanner',
            'value'     => $image,
            'note'      => $note,
            'tabindex'  => 2
        ));

        $fieldset->addField('target_url', 'text', array(
            'label'     => Mage::helper('banner')->__('Target URL'),
            'name'      => 'target_url',
            'value'     => $targetUrl,
            'tabindex'  => 3
        ));

        $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('banner')->__('Sort Order'),
            'name'      => 'sort_order',
            'required'  => true,
            'value'     => $sortOrder,
            'tabindex'  => 4
        ));

        $fieldset->addField('active', 'select', array(
            'label'     => Mage::helper('banner')->__('Status'),
            'name'      => 'active',
            'required'  => false,
            'value'     => $active,
            'values'    => array(
                                0 => Mage::helper('banner')->__('Disable'),
                                1 => Mage::helper('banner')->__('Enable')
                            ),
            'tabindex'  => 5
        ));
        
        return parent::_prepareForm();
    }
}
