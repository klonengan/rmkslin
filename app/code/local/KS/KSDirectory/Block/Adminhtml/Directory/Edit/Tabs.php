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
 *KS_KSDirectory_Block_Adminhtml_Directory_Edit_Tabs
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Block_Adminhtml_Directory_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('directory_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ksdirectory')->__('Directory'));
    }

    protected function _beforeToHtml()
    {
        $blockAction = strtolower(trim($this->getRequest()->getActionName()));
        if($blockAction == 'newprovince' || $blockAction == 'editprovince')
        {
            $this->addTab('directory_section', array(
                'label'     => Mage::helper('ksdirectory')->__('Province'),
                'title'     => Mage::helper('ksdirectory')->__('Province'),
                'content'   => $this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit_tab_province')->toHtml()
            ));
        }
        elseif($blockAction == 'newregency' || $blockAction == 'editregency')
        {
            $this->addTab('directory_section', array(
                'label'     => Mage::helper('ksdirectory')->__('Regency/City'),
                'title'     => Mage::helper('ksdirectory')->__('Regency/City'),
                'content'   => $this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit_tab_regency')->toHtml()
            ));
        }
        elseif($blockAction == 'newsubdistrict' || $blockAction == 'editsubdistrict')
        {
            $this->addTab('directory_section', array(
                'label'     => Mage::helper('ksdirectory')->__('Sub-District'),
                'title'     => Mage::helper('ksdirectory')->__('Sub-District'),
                'content'   => $this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit_tab_subdistrict')->toHtml()
            ));
        }
        elseif($blockAction == 'newvillage' || $blockAction == 'editvillage')
        {
            $this->addTab('directory_section', array(
                'label'     => Mage::helper('ksdirectory')->__('Village'),
                'title'     => Mage::helper('ksdirectory')->__('Village'),
                'content'   => $this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit_tab_village')->toHtml()
            ));
        }
        
        
		return parent::_beforeToHtml();
    }
}