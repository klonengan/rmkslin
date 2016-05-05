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
 * KS_StoreLocator_Block_Adminhtml_Store
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_StoreLocator_Block_Adminhtml_Store extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {

        parent::__construct();
        
        $this->_controller = 'adminhtml_store';
        $this->_blockGroup = 'storelocator';
        $this->_headerText = Mage::helper('storelocator')->__('Store Locator');

        if(! $this->_isAllowedAction("add")) {
            $this->_removeButton('add');
        }

    }

    protected function _prepareLayout() {

        $this->setChild('grid', $this->getLayout()->createBlock('storelocator/adminhtml_store_grid', 'storelocator.grid'));
		
        return parent::_prepareLayout();
    }

    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }

    protected function _isAllowedAction($action) {
        return Mage::getSingleton('admin/session')->isAllowed("ksall/storelocator/actions/".$action);
    }

}