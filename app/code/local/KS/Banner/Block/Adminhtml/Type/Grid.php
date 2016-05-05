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
 * KS_Banner_Block_Adminhtml_Type_Grid
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */
 
class KS_Banner_Block_Adminhtml_Type_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('type_grid');
        $this->setUseAjax(false);
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('banner/type')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('banner')->__('ID'),
            'align'     =>'left',
            'type'      => 'number',
            'index'     => 'id'
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('banner')->__('Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('width', array(
            'header'    => Mage::helper('banner')->__('Width'),
            'align'     =>'left',
            'index'     => 'width'
        ));

        $this->addColumn('height', array(
            'header'    => Mage::helper('banner')->__('Height'),
            'align'     =>'left',
            'index'     => 'height'
        ));

        if($this->_isAllowedAction("edit")){
            $actions[0] = array(
                'caption'   => Mage::helper('banner')->__('Edit'),
                'url'       => array('base'=> '*/*/edittype'),
                'field'     => 'id'
            );
            $this->addColumn('action',
                array(
                'header'    =>  Mage::helper('banner')->__('Action'),
                'type'      => 'action',
                'actions'   => $actions,
                'getter'    => 'getId',
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
                'width'     => '90'
            ));
        }

        return parent::_prepareColumns();
    }

    protected function _isAllowedAction($action) {
        return Mage::getSingleton('admin/session')->isAllowed("ksall/banner/banner_type/".$action);
    }
}