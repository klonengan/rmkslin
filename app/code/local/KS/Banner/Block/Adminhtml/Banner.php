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
 * KS_Banner_Block_Adminhtml_Banner
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_Banner_Block_Adminhtml_Banner extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {

        parent::__construct();
        
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'banner';
        $this->_headerText = Mage::helper('banner')->__('Banner Content Management');

        $this->_removeButton('add');
        if($this->_isAllowedAction("add")) {
            $this->_addButton('addbannercontent', array(
                'label'     => Mage::helper('banner')->__('Add Banner'),
                'onclick'   => 'setLocation(\'' . $this->_getAddBannerUrl() . '\')',
                'class'     => 'scalable add',
            ), -1);
        }
    }

    protected function _prepareLayout() {

        $this->setChild('grid', $this->getLayout()->createBlock('banner/adminhtml_banner_grid', 'banner.grid'));
		
        return parent::_prepareLayout();
    }

    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }

    protected function _getAddBannerUrl(){
        return $this->getUrl('*/*/addbanner');
    }

    protected function _isAllowedAction($action) {
        return Mage::getSingleton('admin/session')->isAllowed("ksall/banner/banner_list/".$action);
    }

}