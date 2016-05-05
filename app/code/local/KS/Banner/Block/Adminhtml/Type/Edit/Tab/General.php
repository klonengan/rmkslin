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
 * KS_Banner_Block_Adminhtml_Type_Edit_Tab_General
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_Banner_Block_Adminhtml_Type_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $data = Mage::registry('banner_type_data');

        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('banner_form', array('legend'=>Mage::helper('banner')->__('General')));
        
        $name = '';
        $width = '';
        $height = '';
        if(count($data) > 0)
        {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
                'value'     => $data->getId()
            ));
            $name = $data->getName();
            $width = $data->getWidth();
            $height = $data->getHeight();
        }

        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('banner')->__('Name'),
            'name'      => 'name',
            'value'     => $name,
            'tabindex'  => 1
        ));

        $fieldset->addField('width', 'text', array(
            'label'     => Mage::helper('banner')->__('Width'),
            'name'      => 'width',
            'value'     => $width,
            'tabindex'  => 2
        ));

        $fieldset->addField('height', 'text', array(
            'label'     => Mage::helper('banner')->__('Height'),
            'name'      => 'height',
            'value'     => $height,
            'tabindex'  => 3
        ));

        return parent::_prepareForm();
    }
}
