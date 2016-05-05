<?php
class KS_Warehouse_Model_Resource_Stock_Status extends Mage_CatalogInventory_Model_Resource_Stock_Status
{

    /**
     * Retrieve product status
     * Return array as key product id, value - stock status
     *
     * @param int|array $productIds
     * @param int $websiteId
     * @param int $stockId
     * @return array
     */
    public function getProductStatus($productIds, $websiteId, $stockId = 1)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
		
		$warehouse_id = Mage::helper('kswarehouse')->isWarehouseMode();
		$cols = $warehouse_id ? array('product_id') : array('product_id','stock_status');
		$table_name = $this->getMainTable();
        $select = $this->_getReadAdapter()->select()
            ->from($table_name, $cols)
            ->where($table_name.'.product_id IN(?)', $productIds)
            ->where('stock_id=?', (int)$stockId)
            ->where('website_id=?', (int)$websiteId);
		if($warehouse_id){
			$select->joinLeft(array('w' => $this->getTable('ks_warehouse/stock')),
				 $table_name.'.product_id=w.product_id AND w.location_id = '.$warehouse_id,
				array()
			)->columns(new Zend_Db_Expr("IFNULL( w.is_instock, ".$table_name.".`stock_status`) AS stock_status"));
		}
        return $this->_getReadAdapter()->fetchPairs($select);
    }

}
