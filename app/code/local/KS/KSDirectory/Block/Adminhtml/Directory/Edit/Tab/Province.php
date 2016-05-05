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
 * KS_KSDirectory_Block_Adminhtml_Directory_Edit_Tab_Province
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Block_Adminhtml_Directory_Edit_Tab_Province extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $_model = Mage::registry('ksdirectory_province_data');
        $blockAction = strtolower(trim($this->getRequest()->getActionName()));

        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('ksdirectory_form', array('legend'=>Mage::helper('ksdirectory')->__('Province')));

        if( $_model && $_model->getId())
        {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
                'value'     => $_model->getId()
            ));
        }
        
        $fieldset->addField('country_code', 'select', array(
            'label'     => Mage::helper('ksdirectory')->__('Country'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'country_code',
            'value'     => $_model->getCountryCode() ? $_model->getCountryCode() : 'ID',
            'values'    => Mage::getModel('ksdirectory/source_country')->toOptionArray(),
            'tabindex'  => 1
        ));

        $fieldset->addField('province_name', 'text', array(
            'label'     => Mage::helper('ksdirectory')->__('Province Name'),
            'name'      => 'province_name',
            'required'  => true,
            'value'     => $_model->getProvinceName(),
            'tabindex'  => 2
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
            'tabindex'  => 3
        ));
        
        return parent::_prepareForm();
    }
}
