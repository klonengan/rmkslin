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
 * KS_KSDirectory_Block_Adminhtml_Directory_Edit
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Block_Adminhtml_Directory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
    	parent::__construct();

        $this->_objectId    = 'id';
        $this->_blockGroup  = 'ksdirectory';
        $this->_controller  = 'adminhtml_directory';

        $this->_removeButton('delete');
        $this->_updateButtonLabels();
        

    }

    protected function _updateButtonLabels()
    {
        $pageAction = strtolower(trim($this->getRequest()->getActionName()));
        if($pageAction == 'newprovince' || $pageAction == 'editprovince')
        {
            $this->_updateButton('save', 'label', Mage::helper('ksdirectory')->__('Save Province'));
            $this->_updateButton('delete', 'label', Mage::helper('ksdirectory')->__('Delete Province'));
        }
        elseif($pageAction == 'newregency' || $pageAction == 'editregency')
        {
            $this->_updateButton('save', 'label', Mage::helper('ksdirectory')->__('Save Regency/City'));
            $this->_updateButton('delete', 'label', Mage::helper('ksdirectory')->__('Delete Regency/City'));
        }
        elseif($pageAction == 'newsubdistrict' || $pageAction == 'editsubdistrict')
        {
            $this->_updateButton('save', 'label', Mage::helper('ksdirectory')->__('Save Sub-District'));
            $this->_updateButton('delete', 'label', Mage::helper('ksdirectory')->__('Delete Sub-District'));
        }
        elseif($pageAction == 'newvillage' || $pageAction == 'editvillage')
        {
            $this->_updateButton('save', 'label', Mage::helper('ksdirectory')->__('Save Village'));
            $this->_updateButton('delete', 'label', Mage::helper('ksdirectory')->__('Delete Village'));
        }
    }

    // public function getBackUrl()
    // {
    //     return $this->getUrl('*/*/index');
    // }

    public function getHeaderText()
    {
        $pageAction = strtolower(trim($this->getRequest()->getActionName()));
        if($pageAction == 'newprovince') 
        {
            return Mage::helper('ksdirectory')->__('Add New Province');
        }
        elseif($pageAction == 'editprovince') 
        {
            return Mage::helper('ksdirectory')->__('Edit Province');
        }
        elseif($pageAction == 'newregency') 
        {
            return Mage::helper('ksdirectory')->__('Add New Regency/City');
        }
        elseif($pageAction == 'editregency') 
        {
            return Mage::helper('ksdirectory')->__('Edit Regency/City');
        }
        elseif($pageAction == 'newsubdistrict') 
        {
            return Mage::helper('ksdirectory')->__('Add New Sub-District');
        }
        elseif($pageAction == 'editsubdistrict') 
        {
            return Mage::helper('ksdirectory')->__('Edit Sub-District');
        }
        elseif($pageAction == 'newvillage') 
        {
            return Mage::helper('ksdirectory')->__('Add New Village');
        }
        elseif($pageAction == 'editvillage') 
        {
            return Mage::helper('ksdirectory')->__('Edit Village');
        }
    }

    protected function _isAllowedAction($action) {
        return Mage::getSingleton('admin/session')->isAllowed("ksall/ksdirectory/actions/".$action);
    }
}