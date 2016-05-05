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
 * KS_StoreLocator_Block_Adminhtml_Store_Grid
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */
 
class KS_StoreLocator_Block_Adminhtml_Store_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('storelocator_store_grid');
        $this->setUseAjax(false);
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('storelocator/store')->getCollection();
        //$collection->addFieldToFilter('super_hide', 0);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // $this->addColumn('id', array(
        //     'header'    => Mage::helper('storelocator')->__('ID'),
        //     'align'     =>'left',
        //     'index'     => 'id'
        // ));
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('storelocator')->__('Created Date'),
            'align'     =>'left',
            'type'      => 'datetime',
            'index'     => 'created_at'
        ));

        $this->addColumn('province', array(
            'header'    => Mage::helper('storelocator')->__('Province'),
            'align'     =>'left',
            'width'     => '70px',
            'index'     => 'province'
        ));

        $this->addColumn('city', array(
            'header'    => Mage::helper('storelocator')->__('City'),
            'align'     =>'left',
            'index'     => 'city'
        ));

        $this->addColumn('store_name', array(
            'header'    => Mage::helper('storelocator')->__('Name'),
            'align'     =>'left',
            'index'     => 'store_name'
        ));

        $this->addColumn('store_address1', array(
            'header'    => Mage::helper('storelocator')->__('Address Line 1'),
            'align'     =>'left',
            'index'     => 'store_address1'
        ));

        $this->addColumn('store_address2', array(
            'header'    => Mage::helper('storelocator')->__('Address Line 2'),
            'align'     =>'left',
            'index'     => 'store_address2'
        ));

        $this->addColumn('store_phone', array(
            'header'    => Mage::helper('storelocator')->__('Phone'),
            'align'     =>'left',
            'index'     => 'store_phone'
        ));

        $this->addColumn('store_fax', array(
            'header'    => Mage::helper('storelocator')->__('Fax'),
            'align'     =>'left',
            'index'     => 'store_fax'
        ));

        $this->addColumn('store_email', array(
            'header'    => Mage::helper('storelocator')->__('Email'),
            'align'     =>'left',
            'index'     => 'store_email'
        ));

        $this->addColumn('store_operations', array(
            'header'    => Mage::helper('storelocator')->__('Store Operations'),
            'align'     =>'left',
            'index'     => 'store_operations',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'KS_StoreLocator_Block_Adminhtml_Store_Grid_Column_Renderer_Operations'
        ));

        $this->addColumn('google_latitude', array(
            'header'    => Mage::helper('storelocator')->__('Latitude'),
            'align'     =>'left',
            'index'     => 'google_latitude'
        ));

        $this->addColumn('google_longitude', array(
            'header'    => Mage::helper('storelocator')->__('Longitude'),
            'align'     =>'left',
            'index'     => 'google_longitude'
        ));
        
        $this->addColumn('is_hotspot', array(
            'header'    => Mage::helper('storelocator')->__('Hot Spot'),
            'align'     => 'left',
            'index'     => 'is_hotspot',
            'type'      => 'options',
            'options'   => array(
                    0 => Mage::helper('storelocator')->__('No'),
                    1 => Mage::helper('storelocator')->__('Yes')
            )
        ));
        
        $this->addColumn('is_active', array(
            'header'    => Mage::helper('storelocator')->__('Status'),
            'align'     => 'left',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                    0 => Mage::helper('storelocator')->__('Disable'),
                    1 => Mage::helper('storelocator')->__('Enable')
            )
        ));

        if($this->_isAllowedAction("edit")) {
            $actions[0] = array(
                'caption'   => Mage::helper('storelocator')->__('Edit'),
                'url'       => array('base'=> '*/*/edit'),
                'field'     => 'id'
            );
        }

        if($this->_isAllowedAction("edit")) {
            $this->addColumn('action',
                array(
                'header'    =>  Mage::helper('storelocator')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => $actions,
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
            ));
        }

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        if($this->_isAllowedAction("edit")) {
            return $this->getUrl('*/*/edit', array('id' => $row->getId()));
        }
        return false;
    }

    protected function _isAllowedAction($action) {
        return Mage::getSingleton('admin/session')->isAllowed("ksall/storelocator/actions/".$action);
    }
}