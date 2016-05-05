<?php
class KS_Warehouse_Model_Resource_Stock_Item extends Mage_CatalogInventory_Model_Resource_Stock_Item
{
    /**
     * Loading stock item data by product
     *
     * @param Mage_CatalogInventory_Model_Stock_Item $item
     * @param int $productId
     * @return Mage_CatalogInventory_Model_Resource_Stock_Item
     */
    public function loadByProductId(Mage_CatalogInventory_Model_Stock_Item $item, $productId)
    {
       $table_name = $this->getTable('cataloginventory/stock_item');
	   
	    $select = $this->_getLoadSelect('product_id', $productId, $item)
            ->where('stock_id = :stock_id');
		
		if($warehouse_id = Mage::helper('kswarehouse')->isWarehouseMode()){
			$select->joinLeft(array('w' => $this->getTable('ks_warehouse/stock')),
				 $table_name.'.product_id=w.product_id AND w.location_id = '.$warehouse_id,
				array()
			)->columns(new Zend_Db_Expr("IFNULL( w.qty, ".$table_name.".qty) AS qty"))
			->columns(new Zend_Db_Expr("IFNULL( w.is_instock, ".$table_name.".`is_in_stock`) AS is_in_stock"));
		}
        $data = $this->_getReadAdapter()->fetchRow($select, array(':stock_id' => $item->getStockId()));
        if ($data) {
            $item->setData($data);
        }
        $this->_afterLoad($item);
        return $this;
    }

    /**
     * Add join for catalog in stock field to product collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
     * @return Mage_CatalogInventory_Model_Resource_Stock_Item
     */
    public function addCatalogInventoryToProductCollection($productCollection)
    {
        $adapter = $this->_getReadAdapter();
        $isManageStock = (int)Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
        $stockExpr = $adapter->getCheckSql('cisi.use_config_manage_stock = 1', $isManageStock, 'cisi.manage_stock');
        $stockExpr = $adapter->getCheckSql("({$stockExpr} = 1)", 'cisi.is_in_stock', '1');

        $productCollection->joinTable(
            array('cisi' => 'cataloginventory/stock_item'),
            'product_id=entity_id',
            array(
                'is_saleable' => new Zend_Db_Expr($stockExpr),
                'inventory_in_stock' => 'is_in_stock'
            ),
            null,
            'left'
        );
        return $this;
    }

    /**
     * Use qty correction for qty column update
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $table
     * @return array
     */
    protected function _prepareDataForTable(Varien_Object $object, $table)
    {
        $data = parent::_prepareDataForTable($object, $table);
        if (!$object->isObjectNew() && $object->getQtyCorrection()) {
            $qty = abs($object->getQtyCorrection());
            if ($object->getQtyCorrection() < 0) {
                $data['qty'] = new Zend_Db_Expr('qty-' . $qty);
            } else {
                $data['qty'] = new Zend_Db_Expr('qty+' . $object->getQtyCorrection());
            }
        }
        return $data;
    }
}
