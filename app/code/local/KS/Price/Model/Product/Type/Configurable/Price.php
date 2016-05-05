<?php
class KS_Price_Model_Product_Type_Configurable_Price extends Mage_Catalog_Model_Product_Type_Configurable_Price
{
    /**
     * Default action to get price of product
     *
     * @return decimal
     */
    public function getPrice($product)
    {
		if($product->getWarehouse()){
			$ids = Mage::getModel('catalog/product_type_configurable')->getUsedProductIds($product);
			if(is_array($ids) && count($ids) && $price = $this->_getMinWarehousePrice($product, $ids))
				return $price->getPrice();
				
		}
        return $product->getData('price');
    }
	
	private function _getMinWarehousePrice($product, $child_ids = array()){
		$warehouse_id 	= $product->getWarehouse()->getId();
		if($warehouse_id){
			$_warehouse_ids =  	array($warehouse_id);
							
			$warehouse_product_price_collection = Mage::getModel('ksprice/price')->getCollection()
			->addFieldToFilter('product_id', array('in' => $child_ids ) )
			->addFieldToFilter('location_id', array('eq' => $_warehouse_ids ) );
			
			$warehouse_product_price_collection->getSelect()->order('price ASC')->limit(1);
			if($warehouse_product_price_collection->getSize()){
				
				return $warehouse_product_price_collection->getFirstItem();
			}
		}
		
		return false;
	}
	

    /**
     * Get product final price
     *
     * @param   double $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getFinalPrice($qty=null, $product)
    {
		$selectedAttributes = array();
		if ($product->getCustomOption('attributes')) {
			$selectedAttributes = unserialize($product->getCustomOption('attributes')->getValue());
		}
		if (sizeof($selectedAttributes)) {
			return $this->getSimpleProductPrice($qty, $product);
		}elseif($product->getWarehouse()){
			$ids = Mage::getModel('catalog/product_type_configurable')->getUsedProductIds($product);
			if(is_array($ids) && count($ids) && $item = $this->_getMinWarehousePrice($product, $ids)){
				$end_product = Mage::getModel("catalog/product")->load($item->getProductId());
				return $end_product->getFinalPrice($qty);
			}
		}
		
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $basePrice = $this->getBasePrice($product, $qty);
        $finalPrice = $basePrice;
        $product->setFinalPrice($finalPrice);
        Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => $qty));
        $finalPrice = $product->getData('final_price');

        $finalPrice += $this->getTotalConfigurableItemsPrice($product, $finalPrice);
        $finalPrice += $this->_applyOptionsPrice($product, $qty, $basePrice) - $basePrice;
        $finalPrice = max(0, $finalPrice);

        $product->setFinalPrice($finalPrice);
        return $finalPrice;
    }

	public function getSimpleProductPrice($qty=null, $product)
    {
        $cfgId = $product->getId();
        $product->getTypeInstance(true)
            ->setStoreFilter($product->getStore(), $product);
        $attributes = $product->getTypeInstance(true)
            ->getConfigurableAttributes($product);
        $selectedAttributes = array();
        if ($product->getCustomOption('attributes')) {
            $selectedAttributes = unserialize($product->getCustomOption('attributes')->getValue());
        }
        $db = Mage::getSingleton('core/resource')->getConnection('core_read');
        $dbMeta = Mage::getSingleton('core/resource');
        $sql = <<<SQL
SELECT main_table.entity_id FROM {$dbMeta->getTableName('catalog/product')} `main_table` INNER JOIN
{$dbMeta->getTableName('catalog/product_super_link')} `sl` ON sl.parent_id = {$cfgId}
SQL;
        foreach($selectedAttributes as $attributeId => $optionId) {
            $alias = "a{$attributeId}";
            $sql .= ' INNER JOIN ' . $dbMeta->getTableName('catalog/product') . "_int" . " $alias ON $alias.entity_id = main_table.entity_id AND $alias.attribute_id = $attributeId AND $alias.value = $optionId AND $alias.entity_id = sl.product_id";
        }

        $id = $db->fetchOne($sql);
        $end_product = Mage::getModel("catalog/product")->load($id);
		
		return $end_product->getFinalPrice($qty);
    }

    /**
     * Get Total price for configurable items
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $finalPrice
     * @return float
     */
    public function getTotalConfigurableItemsPrice($product, $finalPrice)
    {
        $price = 0.0;

        $product->getTypeInstance(true)
                ->setStoreFilter($product->getStore(), $product);
        $attributes = $product->getTypeInstance(true)
                ->getConfigurableAttributes($product);

        $selectedAttributes = array();
        if ($product->getCustomOption('attributes')) {
            $selectedAttributes = unserialize($product->getCustomOption('attributes')->getValue());
        }

        foreach ($attributes as $attribute) {
            $attributeId = $attribute->getProductAttribute()->getId();
            $value = $this->_getValueByIndex(
                $attribute->getPrices() ? $attribute->getPrices() : array(),
                isset($selectedAttributes[$attributeId]) ? $selectedAttributes[$attributeId] : null
            );
            $product->setParentId(true);
            if ($value) {
                if ($value['pricing_value'] != 0) {
                    $product->setConfigurablePrice($this->_calcSelectionPrice($value, $finalPrice));
                    Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
                        array('product' => $product)
                    );
                    $price += $product->getConfigurablePrice();
                }
            }
        }
        return $price;
    }

    /**
     * Calculate configurable product selection price
     *
     * @param   array $priceInfo
     * @param   decimal $productPrice
     * @return  decimal
     */
    protected function _calcSelectionPrice($priceInfo, $productPrice)
    {
        if($priceInfo['is_percent']) {
            $ratio = $priceInfo['pricing_value']/100;
            $price = $productPrice * $ratio;
        } else {
            $price = $priceInfo['pricing_value'];
        }
        return $price;
    }

    protected function _getValueByIndex($values, $index) {
        foreach ($values as $value) {
            if($value['value_index'] == $index) {
                return $value;
            }
        }
        return false;
    }
}
