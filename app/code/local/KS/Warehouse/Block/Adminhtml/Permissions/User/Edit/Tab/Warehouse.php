<?php
/*--------------------------------------------------------------------------
*
*   Modul KS_Club
*   Version 0.1.0
*   Created February 12, 2016
*   Developed by Didi Kusnadi (jalapro08@gmail.com)
*   Copyright © kemana.com - 2016
*
--------------------------------------------------------------------------*/
class KS_Warehouse_Block_Adminhtml_Permissions_User_Edit_Tab_Warehouse
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Selected API2 roles for grid
     *
     * @var array
     */
    protected $_selectedRoles;

    /**
     * Constructor
     * Prepare grid parameters
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('xwarehouses_section')
            ->setDefaultSort('sort_order')
            ->setDefaultDir(Varien_Db_Select::SQL_ASC)
            ->setTitle($this->__('Related Store'))
            ->setUseAjax(true);
    }
	
    protected function _getUser()
    {
	   return Mage::registry('permissions_user');
    }
	
    protected function _getUserToWarehouse()
    {
       if($this->_getUser() && $this->_getUser()->getId() ){
		   $collection = Mage::getModel('ks_warehouse/location_admin')->getCollection()
		   ->addFieldToFilter('user_id', array('eq' => $this->_getUser()->getId() ));
		   if($collection->getSize()){
			   return $collection;
		   }
	   }
	   
	   return false;
    }
	

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ks_warehouse/location')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
		$this->addColumn('in_warehouses', array(
			'header_css_class'  => 'a-center',
			'type'              => 'checkbox',
			'name'              => 'in_warehouses',
			'values'            => $this->_getSelectedWarehouses(),
			'align'             => 'center',
			'index'             => 'id',
			'use_index'			=> true ,
			'renderer'  		=> "KS_Warehouse_Block_Adminhtml_Permissions_User_Edit_Tab_Warehouse_Renderer_Checkbox",
			'inline_css'  		=> 'warehouses_collection',
			'prefix_identifier'	=> 'warehouses_collection',
			'unset_item'		=> 'unset_warehouse',
			'set_item'			=> 'set_warehouse',
			'headeronclick'		=> 'checkUncheckAll(this,\'warehouses_collection\',\'unset_warehouse\',\'set_warehouse\');',
		));

        $this->addColumn('name', array(
            'header'    => $this->__('Name'),
            'index'     => 'name'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Add custom column filter to collection
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mage_Api2_Block_Adminhtml_Permissions_User_Edit_Tab_Roles
     */
  /*  protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'assigned_user_role') {
            $userRoles = $this->_getSelectedRoles();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $userRoles));
            } elseif (!empty($userRoles)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $userRoles));
            } else {
                $this->getCollection();
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }*/

    /**
     * Retrieve selected studio
     *
     * @return array
     */
    protected function _getSelectedWarehouses()
    {
        $_warehouses = $this->getUserWarehouses();
        if (!is_array($_warehouses)) {
            $_warehouses = array_keys($this->getSelectedWarehouse());
        }
        return $_warehouses;
    }

    /**
     * Retrieve studio
     *
     * @return array
     */
    public function getSelectedWarehouse()
    {
        $_warehouses = array();
		if($warehouses = $this->_getUserToWarehouse()){
			foreach ($warehouses as $_warehouse) {
				$_warehouses[$_warehouse->getLocationId()] = array('position' => 0);
			}
		}
        return $_warehouses;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Related Store');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Related Store');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get controller action url for grid ajax actions
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'adminhtml/warehouse/gridPermissionWarehouse',
            array('user_id' => Mage::registry('permissions_user')->getUserId())
        );
    }
}
