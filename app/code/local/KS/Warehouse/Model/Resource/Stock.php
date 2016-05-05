<?php
class KS_Warehouse_Model_Resource_Stock extends Mage_CatalogInventory_Model_Resource_Stock
{
    /**
     * Get stock items data for requested products
     *
     * @param Mage_CatalogInventory_Model_Stock $stock
     * @param array $productIds
     * @param bool $lockRows
     * @return array
     */
    public function getProductsStock($stock, $productIds, $lockRows = false)
    {
        if (empty($productIds)) {
            return array();
        }
        $itemTable = $this->getTable('cataloginventory/stock_item');
        $productTable = $this->getTable('catalog/product');
		if($warehouse_id = Mage::helper('kswarehouse')->isWarehouseMode()){
        	$select = $this->_getWriteAdapter()->select()
            ->from(array('si' => $itemTable))
            ->join(array('p' => $productTable), 'p.entity_id=si.product_id', array('type_id'))
            ->where('si.stock_id=?', $stock->getId())
            ->where('si.product_id IN(?)', $productIds)
			->joinLeft(array('w' => $this->getTable('ks_warehouse/stock')),
				 'si.product_id=w.product_id AND w.location_id = '.$warehouse_id,
				array()
			)->columns(new Zend_Db_Expr("IFNULL( w.qty, si.qty) AS qty"))
			->columns(new Zend_Db_Expr("IFNULL( w.is_instock, si.`is_in_stock`) AS is_in_stock"))
			->forUpdate($lockRows);
		}else{
			$select = $this->_getWriteAdapter()->select()
				->from(array('si' => $itemTable))
				->join(array('p' => $productTable), 'p.entity_id=si.product_id', array('type_id'))
				->where('stock_id=?', $stock->getId())
				->where('product_id IN(?)', $productIds)
				->forUpdate($lockRows);
		}
        return $this->_getWriteAdapter()->fetchAll($select);
    }
}
