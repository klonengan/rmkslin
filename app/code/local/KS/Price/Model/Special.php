<?php
class KS_Price_Model_Special extends Mage_Core_Model_Abstract {

	var $banner_filter;

	protected function _construct(){
		parent::_construct();
		$this->_init( 'ksprice/special' );
	}
	
	public function getWarehousePrices($product)	{
		
		$warehouse_product_price_collection = $this->getCollection()
		->addFieldToFilter('product_id', array('eq' => $product->getData('entity_id') ) );
		$warehouse_product_price_collection->getSelect()->order('id ASC');
		$prices = array();
		if($warehouse_product_price_collection->getSize()){
			
			foreach($warehouse_product_price_collection as $_price){
				$prices[] = $_price->getData();
			}
		}
		return $prices;
	}
	
}
