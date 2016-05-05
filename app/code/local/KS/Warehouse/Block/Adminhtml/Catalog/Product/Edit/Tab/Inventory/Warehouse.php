<?php
class KS_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Inventory_Warehouse
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory
{

    /**
     * Initialize block
     */
    public function __construct()
    {
        $this->setTemplate('ks/warehouse/catalog/product/tab/inventory/warehouse.phtml');
    }

    /**
     * Prepare global layout
     * Add "Add tier" button to layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
     */
    public function getAddButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('catalog')->__('Add Warehouse Stock'),
                'onclick' => 'return warehouseStockControl.addItem()',
                'class' => 'add'
            ));
		return $button->toHtml();
    }
	
	public function getWarehouse(){
		return Mage::getModel('ks_warehouse/location')->toOptionArray();
	}

	public function getValues(){
		return Mage::getModel('ks_warehouse/stock')->getWarehouseStock($this->getProduct());
	}
	
}
