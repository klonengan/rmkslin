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
 * KS_StoreLocator_Block_Adminhtml_Store_Edit
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_StoreLocator_Block_Adminhtml_Store_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
    	parent::__construct();

        $this->_objectId    = 'id';
        $this->_blockGroup  = 'storelocator';
        $this->_controller  = 'adminhtml_store';

        if(! $this->_isAllowedAction('delete'))
        {
            $this->_removeButton('delete');
        }

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }

    public function getHeaderText()
    {
        $pageAction = strtolower(trim($this->getRequest()->getActionName()));
        if( $pageAction == 'edit') 
        {
            return Mage::helper('storelocator')->__('Edit Store Data');
        }
        elseif( $pageAction == 'new') 
        {
            return Mage::helper('storelocator')->__('Add New Store');
        }
    }

    protected function _isAllowedAction($action) {
        return Mage::getSingleton('admin/session')->isAllowed("ksall/storelocator/actions/".$action);
    }
}