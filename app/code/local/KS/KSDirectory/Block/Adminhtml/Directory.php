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
 * KS_KSDirectory_Block_Adminhtml_Directory
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Block_Adminhtml_Directory extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {

        parent::__construct();
        
        $this->_controller = 'adminhtml_directory';
        $this->_blockGroup = 'ksdirectory';
        $this->_headerText = Mage::helper('ksdirectory')->__('Directory Management');

        $this->_removeButton('add');
        if($this->_isAllowedAction("add")) {

            if(Mage::helper('ksdirectory')->isProvinceEnable()){
                $this->_addButton('addprovince', array(
                    'label'     => Mage::helper('ksdirectory')->__('Add Province'),
                    'onclick'   => 'setLocation(\'' . $this->_getAddProvinceUrl() . '\')',
                    'class'     => 'scalable add',
                ), -1);

                if(Mage::helper('ksdirectory')->isRegencyEnable()){
                    $this->_addButton('addregency', array(
                        'label'     => Mage::helper('ksdirectory')->__('Add Regency'),
                        'onclick'   => 'setLocation(\'' . $this->_getAddRegencyUrl() . '\')',
                        'class'     => 'scalable add',
                    ), -1);

                    if(Mage::helper('ksdirectory')->isSubdistrictEnable()){
                        $this->_addButton('addsubdistrict', array(
                            'label'     => Mage::helper('ksdirectory')->__('Add Sub-District'),
                            'onclick'   => 'setLocation(\'' . $this->_getAddSubdistrictUrl() . '\')',
                            'class'     => 'scalable add',
                        ), -1);

                        if(Mage::helper('ksdirectory')->isVillageEnable()){
                            $this->_addButton('addvillage', array(
                                'label'     => Mage::helper('ksdirectory')->__('Add Village'),
                                'onclick'   => 'setLocation(\'' . $this->_getAddVillageUrl() . '\')',
                                'class'     => 'scalable add',
                            ), -1);
                        }
                    }
                }
            }
        }
    }

    protected function _prepareLayout() {

        $this->setChild('grid', $this->getLayout()->createBlock('ksdirectory/adminhtml_directory_grid', 'ksdirectory.grid'));
		
        return parent::_prepareLayout();
    }

    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }

    protected function _getAddProvinceUrl(){
        return $this->getUrl('*/*/newprovince');
    }

    protected function _getAddRegencyUrl(){
        return $this->getUrl('*/*/newregency');
    }

    protected function _getAddSubdistrictUrl(){
        return $this->getUrl('*/*/newsubdistrict');
    }

    protected function _getAddVillageUrl(){
        return $this->getUrl('*/*/newvillage');
    }

    protected function _isAllowedAction($action) {
        return Mage::getSingleton('admin/session')->isAllowed("ksall/ksdirectory/actions/".$action);
    }

}