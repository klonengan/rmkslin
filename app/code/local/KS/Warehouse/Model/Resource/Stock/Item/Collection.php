<?php
class KS_Warehouse_Model_Resource_Stock_Item_Collection extends Mage_CatalogInventory_Model_Resource_Stock_Item_Collection
{

    /**
     * Join Stock Status to collection
     *
     * @param int $storeId
     * @return Mage_CatalogInventory_Model_Resource_Stock_Item_Collection
     */
    public function joinStockStatus($storeId = null)
    {
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        $this->getSelect()->joinLeft(
            array('status_table' => $this->getTable('cataloginventory/stock_status')),
                'main_table.product_id=status_table.product_id'
                . ' AND main_table.stock_id=status_table.stock_id'
                . $this->getConnection()->quoteInto(' AND status_table.website_id=?', $websiteId),
            array('stock_status')
        );
		
		if($warehouse_id = Mage::helper('kswarehouse')->isWarehouseMode()){
			$quote = Mage::getSingleton('checkout/cart')->getQuote();
			$quote->setLocationId($warehouse_id);
		
			$this->getSelect()->joinLeft(
				array('warehouse' => $this->getTable('ks_warehouse/stock')),
					'main_table.product_id=warehouse.product_id'
					. $this->getConnection()->quoteInto(' AND warehouse.location_id = ?', $warehouse_id),
				array()
			)->columns(new Zend_Db_Expr("IFNULL( warehouse.qty, main_table.qty) AS qty"))
			->columns(new Zend_Db_Expr("IFNULL( warehouse.is_instock, `status_table`.`stock_status`) AS stock_status"))
			->columns(new Zend_Db_Expr("IFNULL( warehouse.is_instock, `main_table`.`is_in_stock`) AS is_in_stock"));
		}
        return $this;
    }

}
