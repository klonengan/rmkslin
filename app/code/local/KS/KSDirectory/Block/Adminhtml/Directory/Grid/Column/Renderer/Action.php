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
 * Block KS_PaymentConfirmation_Block_Adminhtml_Csoconfirmation_Grid_Column_Bank
 * Bank Name renderer
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Block_Adminhtml_Directory_Grid_Column_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $html = '<select onchange="varienGridAction.execute(this);" class="action-select">';
        $html .= '<option value="">--Select Action--</option>';
        if(Mage::helper('ksdirectory')->isProvinceEnable()) $html .= '<option value="{&quot;href&quot;:&quot;'.str_replace('/', '\/', $this->getUrl('*/*/editprovince').'gid/'.$row['id']).'&quot;}">'.Mage::helper('ksdirectory')->__('Edit Province').'</option>';
        if(Mage::helper('ksdirectory')->isRegencyEnable()) $html .= '<option value="{&quot;href&quot;:&quot;'.str_replace('/', '\/', $this->getUrl('*/*/editregency').'gid/'.$row['id']).'&quot;}">'.Mage::helper('ksdirectory')->__('Edit Regency').'</option>';
        if(Mage::helper('ksdirectory')->isSubdistrictEnable()) $html .= '<option value="{&quot;href&quot;:&quot;'.str_replace('/', '\/', $this->getUrl('*/*/editsubdistrict').'gid/'.$row['id']).'&quot;}">'.Mage::helper('ksdirectory')->__('Edit Sub-District').'</option>';
        if(Mage::helper('ksdirectory')->isVillageEnable()) $html .= '<option value="{&quot;href&quot;:&quot;'.str_replace('/', '\/', $this->getUrl('*/*/editvillage').'gid/'.$row['id']).'&quot;}">'.Mage::helper('ksdirectory')->__('Edit Village').'</option>';
        $html .= '</select>';
        echo $html;
    }

}
