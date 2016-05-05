<?php
class KS_Price_Model_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{


    /**
     * Default action to get price of product
     *
     * @return decimal
     */
    public function getPrice($product)
    {
		if($product->getWarehouse() && $price = $this->_getWarehousePrice($product)){
			return $price;
		}
        return $product->getData('price');
    }

    /**
     * Get base price with apply Group, Tier, Special prises
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float|null $qty
     *
     * @return float
     */
    public function getBasePrice($product, $qty = null)
    {
        $price = (float)$product->getPrice();

		if($product->getWarehouse()){
			$warehouseSpecialData = $this->_getWarehouseSpecialData($product);
			if($warehouseSpecialData){
				$warehouseSpecialPrice = (float)$this->calculateSpecialPrice($price, $warehouseSpecialData->getPrice(), $warehouseSpecialData->getDateFrom(), $warehouseSpecialData->getDateTo());
				
				$groupPrice = $this->_applyGroupPrice($product, $price);
				$tierPrice = $this->_applyTierPrice($product, $qty, $price);
				$specialPrice = $this->_applySpecialPrice($product, $price);
				return min($groupPrice, $tierPrice, $specialPrice, $warehouseSpecialPrice);
			}
		}

        return min($this->_applyGroupPrice($product, $price), $this->_applyTierPrice($product, $qty, $price),
            $this->_applySpecialPrice($product, $price)
        );
    }

    /**
     * Retrieve product final price
     *
     * @param float|null $qty
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getFinalPrice($qty = null, $product)
    {
		if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }
		$finalPrice = $this->getBasePrice($product, $qty);

		if($product->getWarehouse()){
        	$warehousePrice = $this->getPrice($product);
			$warehouseFinalPrice = Mage::getModel('catalogrule/rule')->calcProductPriceRule($product,$warehousePrice);

			if(!is_null($warehouseFinalPrice))
				$finalPrice = min($finalPrice,(float)$warehouseFinalPrice);
		}
		
        $product->setFinalPrice($finalPrice);

        Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => $qty));

        $finalPrice = $product->getData('final_price');
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $finalPrice = max(0, $finalPrice);
        $product->setFinalPrice($finalPrice);

        return $finalPrice;
    }
			
	
	private function _getWarehousePrice($product){
		$warehouse_id 	= $product->getWarehouse()->getId();
		if($warehouse_id){
			$_warehouse_ids =  	array($warehouse_id);
							
			$warehouse_product_price_collection = Mage::getModel('ksprice/price')->getCollection()
			->addFieldToFilter('product_id', array('eq' => $product->getId() ) )
			->addFieldToFilter('location_id', array('in' => $_warehouse_ids ) );
			
			$warehouse_product_price_collection->getSelect()->order('location_id DESC')->limit(1);
			if($warehouse_product_price_collection->getSize()){
				
				return (float)$warehouse_product_price_collection->getFirstItem()->getPrice();
			}
		}
		
		return false;
	}
	
	private function _getWarehouseSpecialData($product){
		$warehouse_id 	= $product->getWarehouse()->getId();
		if($warehouse_id){
			$_warehouse_ids =  	array($warehouse_id);
							
			$warehouse_product_price_collection = Mage::getModel('ksprice/special')->getCollection()
			->addFieldToFilter('product_id', array('eq' => $product->getId() ) )
			->addFieldToFilter('location_id', array('in' => $_warehouse_ids ) );

			$warehouse_product_price_collection->getSelect()
			->order('location_id DESC')->limit(1);
			if($warehouse_product_price_collection->getSize()){
				
				return $warehouse_product_price_collection->getFirstItem();
			}
		}
		
		return false;
	}
	
}
