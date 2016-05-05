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
 * KS_KSDirectory_Block_Adminhtml_Directory_Edit_Form
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Block_Adminhtml_Directory_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $blockAction = strtolower(trim($this->getRequest()->getActionName()));
        if($blockAction == 'newprovince' || $blockAction == 'editprovince')
        {
            $url = $this->getUrl('*/*/saveprovince', array('gid' => $this->getRequest()->getParam('gid')));
        }
        elseif($blockAction == 'newregency' || $blockAction == 'editregency')
        {
            $url = $this->getUrl('*/*/saveregency', array('gid' => $this->getRequest()->getParam('gid')));
        }
        elseif($blockAction == 'newsubdistrict' || $blockAction == 'editsubdistrict')
        {
            $url = $this->getUrl('*/*/savesubdistrict', array('gid' => $this->getRequest()->getParam('gid')));
        }
        elseif($blockAction == 'newvillage' || $blockAction == 'editvillage')
        {
            $url = $this->getUrl('*/*/savevillage', array('gid' => $this->getRequest()->getParam('gid')));
        }

        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $url,
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}