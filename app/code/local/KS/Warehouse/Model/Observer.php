<?php
class KS_Warehouse_Model_Observer extends Mage_Core_Block_Abstract
{
	/*
	* save warehouse stock
	*/
    public function saveWarehouseStock($observer)
    {

		if ($actionInstance = Mage::app()->getFrontController()->getAction()) {
			$action = $actionInstance->getFullActionName();
			if ($action == 'adminhtml_catalog_product_save') {

				$product = $observer->getEvent()->getProduct();
		
				$transaction = Mage::getSingleton('core/resource')->getConnection('core_write');

				if(Mage::app()->getRequest()->getPost()){
					//$this->_deleteStoreRelation();
					$data = Mage::app()->getRequest()->getPost();

					//warehouse price
					if(isset($data['product']['warehouse_stock']) && is_array($data['product']['warehouse_stock']) && count($data['product']['warehouse_stock'])){
						
						$table_name = Mage::getSingleton('core/resource')->getTableName('ks_warehouse/stock');
						
						foreach($data['product']['warehouse_stock'] as $_cp){
							
							try {
								$transaction->beginTransaction();
								
								if($_cp['delete'] && $_cp['warehouse_id']){
									$transaction->query("DELETE FROM `".$table_name."` WHERE `location_id` = '$_cp[warehouse_id]' AND `product_id` = '".$product->getData('entity_id')."'");
								}else{								
									$transaction->query("INSERT INTO `".$table_name."` (
									  `product_id`,
									  `location_id`,
									  `qty`,
									  `is_instock`
									) 
									VALUES
									  (
										'".$product->getData('entity_id')."',
										'$_cp[warehouse_id]',
										'$_cp[qty]',
										'$_cp[status]'
									  ) ON DUPLICATE KEY UPDATE `qty` = '$_cp[qty]', `is_instock` = '$_cp[status]'");
								}
								$transaction->commit();
							} catch (Exception $e) {
								$transaction->rollBack(); 
							}
		
						}
						
						//reindex
						$this->reindexQty(array($product->getId()));
					}
					
				}
				return $this;
			}
		}		
    }
	
	/*
	* set product warehouse
	*/
	public function setProductWarehouse($observer){
		$product = $observer->getEvent()->getProduct();	
		if(isset($_COOKIE[KS_Price_Helper_Data::COOKIE_NAME]) && $warehouse_id = (int)$_COOKIE[KS_Price_Helper_Data::COOKIE_NAME]){
			
			$warehouse = Mage::getModel('ks_warehouse/location')->load($warehouse_id);
			$product->setWarehouse($warehouse);
		}
			
	}
	
	/*
	* apply club price
	*/
	public function applyQuoteLocation($observer){
		
		if(isset($_COOKIE[KS_Price_Helper_Data::COOKIE_NAME]) && $warehouse_id = (int)$_COOKIE[KS_Price_Helper_Data::COOKIE_NAME]){
			$quote = Mage::getSingleton('checkout/cart')->getQuote();
			$quote->setLocationId($warehouse_id);
		}
	}
	
	
	public function setWarehouseToProducts($observer){
		if(isset($_COOKIE[KS_Price_Helper_Data::COOKIE_NAME]) && $warehouse_id = (int)$_COOKIE[KS_Price_Helper_Data::COOKIE_NAME]){
			$productCollection = $observer->getEvent()->getCollection();
			$warehouse = Mage::getModel('ks_warehouse/location')->load($warehouse_id);
			foreach ($productCollection as $product) {
				$product->setWarehouse($warehouse);
			}
			return $this;
		}
	}
	/*
	* remove item from cart
	*/
	private function _deleteItemProduct($item_id){
		Mage::getSingleton('checkout/cart')->removeItem($item_id)->save();
	}
	

	/*
	* convert quote to order
	*/
	public function convertQuote($observer){
		$quote = $observer->getEvent()->getQuote();
		$order = $observer->getEvent()->getOrder();
		$order->setLocationId($quote->getLocationId());
	}
	
	/*
	* decrease stock
	*/
	public function orderPlaceAfter($observer)
	{
		$order = $observer->getEvent()->getOrder();
		$transaction = Mage::getSingleton('core/resource')->getConnection('core_write');
		$item = Mage::getSingleton('core/resource')->getTableName('sales/order_item');
		$stock = Mage::getSingleton('core/resource')->getTableName('ks_warehouse/stock');
		$order_id = $order->getId();
		$location_id = $order->getLocationId();
		try {
			$transaction->beginTransaction();
			$transaction->query("UPDATE `".$stock."` AS stock
									JOIN `".$item."` AS item ON stock.`product_id` = item.`product_id`
								SET 
									stock.`is_instock` = IF(stock.`qty` - item.`qty_ordered` <= 0, 0, 1), stock.`qty` = IF(stock.`qty` - item.`qty_ordered` <= 0, 0, stock.`qty` - item.`qty_ordered`)
								WHERE item.order_id = '".$order_id."'  
								AND stock.`location_id` = '".$location_id."'");
			$this->reindexQty($this->_getOrderProductIds($order));
			$transaction->commit();
		} catch (Exception $e) {
			Mage::logException($e);
			$transaction->rollBack(); 
		}

	}
	
	/*
	* re stock
	*/
	public function orderCancelAfter($observer)
	{
		$order = $observer->getEvent()->getOrder();
		$transaction = Mage::getSingleton('core/resource')->getConnection('core_write');
		$item = Mage::getSingleton('core/resource')->getTableName('sales/order_item');
		$stock = Mage::getSingleton('core/resource')->getTableName('ks_warehouse/stock');
		$order_id = $order->getId();
		$location_id = $order->getLocationId();
		
		try {
			$transaction->beginTransaction();
			$transaction->query("UPDATE `".$stock."` AS stock
									JOIN `".$item."` AS item ON stock.`product_id` = item.`product_id`
								SET 
									stock.`is_instock` = IF(stock.`qty` + item.`qty_ordered` > 0, 1, 0), stock.`qty` = IF(stock.`qty` + item.`qty_ordered` > 0, stock.`qty` + item.`qty_ordered`, 0)
								WHERE item.order_id = '".$order_id."'  
								AND stock.`location_id` = '".$location_id."'");
			$this->reindexQty($this->_getOrderProductIds($order));
			$transaction->commit();
		} catch (Exception $e) {
			Mage::logException($e);
			$transaction->rollBack(); 
		}

	}
	
	public function _getOrderProductIds($order){
		$product_ids = array();
		foreach($order->getAllItems() as $_item){
			$product_ids[] = $_item->getProductId();
		}
		return $product_ids;
	}
	
	public function reindexQty($_product_ids = array()){
		
		if(!count($_product_ids)) return;
		
		$transaction = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		try {
			$transaction->beginTransaction();
	
			$stock_item = Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_item');
			$stock_status = Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_status');
			$stock_status_indexer_idx = Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_status_indexer_idx');
			$product_relation = Mage::getSingleton('core/resource')->getTableName('catalog/product_relation');
			$warehouse_stock = Mage::getSingleton('core/resource')->getTableName('ks_warehouse/stock');
			//stock item simple
			$query = "UPDATE  `".$stock_item."` AS stock
						JOIN (
							SELECT 
							  product_id,
							  SUM(qty) AS qty,
							  IF(SUM(qty) > 0, 1, 0) AS stock_status,
							  IF(SUM(qty) > 0, 1, 0) AS is_in_stock 
							FROM
							  `".$warehouse_stock."` 
							WHERE product_id IN (".implode(",", $_product_ids).") 
							GROUP BY product_id
						) AS alias ON alias.product_id = stock.`product_id`
						SET stock.qty = IFNULL(alias.qty, 0), stock.`is_in_stock` = IFNULL(alias.stock_status,0);";

			$transaction->query($query);
			
			//stock status simple
			$query = "UPDATE  `".$stock_status."` AS stock
						JOIN (
							SELECT 
							  product_id,
							  SUM(qty) AS qty,
							  IF(SUM(qty) > 0, 1, 0) AS stock_status,
							  IF(SUM(qty) > 0, 1, 0) AS is_in_stock 
							FROM
							  `".$warehouse_stock."` 
							WHERE product_id IN (".implode(",", $_product_ids).") 
							GROUP BY product_id
						) AS alias ON alias.product_id = stock.`product_id`
						SET stock.qty = IFNULL(alias.qty,0), stock.`stock_status` = IFNULL(alias.stock_status,0);";
			$transaction->query($query);
			
			//stock status simple
			$query = "UPDATE `".$stock_status_indexer_idx."` AS stock
						JOIN (
							SELECT 
							  product_id,
							  SUM(qty) AS qty,
							  IF(SUM(qty) > 0, 1, 0) AS stock_status,
							  IF(SUM(qty) > 0, 1, 0) AS is_in_stock 
							FROM
							  `".$warehouse_stock."` 
							WHERE product_id IN (".implode(",", $_product_ids).") 
							GROUP BY product_id
						) AS alias ON alias.product_id = stock.`product_id`
						SET stock.qty = IFNULL(alias.qty,0), stock.`stock_status` = IFNULL(alias.stock_status,0);";
			$transaction->query($query);
			
			//stock item parent
			$query = "UPDATE  `".$stock_item."` AS stock
						JOIN (
							SELECT 
							  parent_id,
							  SUM(qty) AS qty,
							  IF(SUM(qty) > 0, 1, 0) AS stock_status,
							  IF(SUM(qty) > 0, 1, 0) AS is_in_stock 
							FROM
							  `".$warehouse_stock."` 
							JOIN `".$product_relation."` ON product_id = child_id
							GROUP BY parent_id
						) AS alias ON alias.parent_id = stock.`product_id`
						SET stock.`is_in_stock` = alias.stock_status;";
			$transaction->query($query);
			
			//stock status parent
			$query = "UPDATE  `".$stock_status."` AS stock
						JOIN (
							SELECT 
							  parent_id,
							  SUM(qty) AS qty,
							  IF(SUM(qty) > 0, 1, 0) AS stock_status,
							  IF(SUM(qty) > 0, 1, 0) AS is_in_stock 
							FROM
							  `".$warehouse_stock."` 
							JOIN `".$product_relation."` ON product_id = child_id
							GROUP BY parent_id
						) AS alias ON alias.parent_id = stock.`product_id`
						SET stock.`stock_status` = alias.stock_status;";
			$transaction->query($query);
			
			//stock status parent
			$query = "UPDATE `".$stock_status_indexer_idx."` AS stock
						JOIN (
							SELECT 
							  parent_id,
							  SUM(qty) AS qty,
							  IF(SUM(qty) > 0, 1, 0) AS stock_status,
							  IF(SUM(qty) > 0, 1, 0) AS is_in_stock 
							FROM
							  `".$warehouse_stock."` 
							JOIN `".$product_relation."` ON product_id = child_id
							GROUP BY parent_id
						) AS alias ON alias.parent_id = stock.`product_id`
						SET stock.`stock_status` = alias.stock_status;";
			$transaction->query($query);
			$transaction->commit();
		} catch (Exception $e) {
			Mage::logException($e);
			$transaction->rollBack(); 
		}
	}
	
		
}
