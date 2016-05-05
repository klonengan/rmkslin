<?php
class KS_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Inventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ks/warehouse/catalog/product/tab/inventory.phtml');
    }
	
	public function getWarehouseInventoryHtml(){
		$block = $this->getLayout()->createBlock('kswarehouse/adminhtml_catalog_product_edit_tab_inventory_warehouse');
		return $block->toHtml();
	}

}
