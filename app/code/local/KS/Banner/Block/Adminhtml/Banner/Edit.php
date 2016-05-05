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
 * KS_Banner_Block_Adminhtml_Banner_Edit
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_Banner_Block_Adminhtml_Banner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
    	parent::__construct();

        $this->_objectId    = 'id';
        $this->_blockGroup  = 'banner';
        $this->_controller  = 'adminhtml_banner';

        $this->_removeButton('back');
        $this->_removeButton('delete');
        $this->_addButton('banner_back', array(
            'label'     => Mage::helper('banner')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->_getBackUrl() . '\')',
            'class'     => 'back',
        ), -1);

        if($this->_isAllowed('delete') && strtolower(trim($this->getRequest()->getActionName())) == 'editbanner'){
            $this->_addButton('banner_delete', array(
                'label'     => Mage::helper('banner')->__('Delete Banner'),
                'onclick'   => 'deleteConfirm(\''. Mage::helper('banner')->__('Are you sure you want to DELETE this banner?')
                        .'\', \'' . $this->_getDeleteUrl() . '\')',
                'class'     => 'scalable delete',
            ), -1);
        }
        $this->_updateButton('save', 'label', Mage::helper('banner')->__('Save Banner'));
        

    }

    public function getHeaderText()
    {
        $pageAction = strtolower(trim($this->getRequest()->getActionName()));
        if($pageAction == 'addbanner') 
        {
            return Mage::helper('banner')->__('Add New Banner');
        }
        elseif($pageAction == 'editbanner') 
        {
            return Mage::helper('banner')->__('Edit Banner');
        }
    }

    protected function _getBackUrl(){
        return $this->getUrl('*/*/list');
    }

    protected function _getDeleteUrl(){
        return $this->getUrl('*/*/deletebanner', array('id' => $this->getRequest()->getParam('id')));
    }

    protected function _isAllowed($action) {
        return Mage::getSingleton('admin/session')->isAllowed("ksall/banner/banner_list/".$action);
    }
}