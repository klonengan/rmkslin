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
 * KS_Banner_Block_Adminhtml_Type_Edit
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_Banner_Block_Adminhtml_Type_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
    	parent::__construct();

        $this->_objectId    = 'id';
        $this->_blockGroup  = 'banner';
        $this->_controller  = 'adminhtml_type';

        $this->_removeButton('delete');
        $this->_removeButton('back');
        $this->_addButton('bannertype_back', array(
            'label'     => Mage::helper('banner')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->_getBackUrl() . '\')',
            'class'     => 'back',
        ), -1);
        $this->_updateButton('save', 'label', Mage::helper('banner')->__('Save Banner'));
        

    }

    public function getHeaderText()
    {
        $pageAction = strtolower(trim($this->getRequest()->getActionName()));
        if($pageAction == 'addtype') 
        {
            return Mage::helper('banner')->__('Add New Banner Type');
        }
        elseif($pageAction == 'edittype') 
        {
            return Mage::helper('banner')->__('Edit Banner Type');
        }
    }

    protected function _getBackUrl(){
        return $this->getUrl('*/*/type');
    }

    protected function _isAllowedAction($action) {
        return Mage::getSingleton('admin/session')->isAllowed("ksall/banner/banner_type/".$action);
    }
}