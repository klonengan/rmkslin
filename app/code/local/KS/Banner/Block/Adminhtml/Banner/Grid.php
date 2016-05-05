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
 * KS_Banner_Block_Adminhtml_Banner_Grid
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */
 
class KS_Banner_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('banner_grid');
        $this->setUseAjax(false);
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('banner/list')->getCollection();
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

        $this->addColumn('type_id', array(
            'header'    => Mage::helper('banner')->__('Banner Type'),
            'align'     =>'left',
            'index'     => 'type_id',
            'renderer'  => 'KS_Banner_Block_Adminhtml_Banner_Grid_Column_Renderer_Bannertype',
            'type'      => 'options',
            'options'   => Mage::getSingleton('banner/source_type')->toOptionArray(false),
        ));

        $this->addColumn('category_id', array(
            'header'    => Mage::helper('banner')->__('Category'),
            'align'     =>'left',
            'index'     => 'category_id',
            //'renderer'  => 'KS_Banner_Block_Adminhtml_Banner_Grid_Column_Renderer_Bannercat',
            'type'      => 'options',
            'options'   => Mage::getSingleton('banner/source_category')->toOptionArray(false),
        ));

        $this->addColumn('image', array(
            'header'    => Mage::helper('banner')->__('Image'),
            'align'     =>'left',
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'image',
            'renderer'  => 'KS_Banner_Block_Adminhtml_Banner_Grid_Column_Renderer_Image'
        ));

        $this->addColumn('target_url', array(
            'header'    => Mage::helper('banner')->__('Target URL'),
            'align'     =>'left',
            'index'     => 'target_url'
        ));

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('banner')->__('Sort Order'),
            'align'     =>'left',
            'index'     => 'sort_order'
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('banner')->__('Created At'),
            'align'     =>'left',
            'width'     => '140',
            'index'     => 'created_at'
        ));

        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('banner')->__('Updated At'),
            'align'     =>'left',
            'width'     => '140',
            'index'     => 'updated_at'
        ));

        $this->addColumn('admin_name', array(
            'header'    => Mage::helper('banner')->__('Updated By'),
            'align'     =>'left',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'KS_Banner_Block_Adminhtml_Banner_Grid_Column_Renderer_Admin'
        ));

        if($this->_isAllowedAction("edit")){
            $actions[0] = array(
                'caption'   => Mage::helper('banner')->__('Edit'),
                'url'       => array('base'=> '*/*/editbanner'),
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
        return Mage::getSingleton('admin/session')->isAllowed("ksall/banner/banner_list/".$action);
    }
}