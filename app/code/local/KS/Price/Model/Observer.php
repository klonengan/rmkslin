<?php
class KS_Price_Model_Observer extends Mage_Core_Block_Abstract
{
	/*
	* warehouse pricing form on catalog product
	*/
	public function addWarehousePricingForm($observer)
	{
        $model = Mage::registry('current_product');
		if(!$model)
			return;
		
		$form = $observer->getForm();
				
		foreach($form->getElements() as $e){
			if($e->getData('legend') == 'Prices'){
				$e->getData('html_id');
				
				$fieldset = $form->getElement($e->getData('html_id'));
		
				$fieldset->addField('warehouse_price', 'text', array(
						'name'=>'warehouse_price',
						'class'=>'requried-entry',
						'value'=>''
				),'price');
				
				$layout = Mage::getSingleton('core/layout');
				$form->getElement('warehouse_price')->setRenderer(
					$layout->createBlock('ksprice/adminhtml_catalog_product_edit_tab_price_price')
				);
				
				$fieldset->addField('warehouse_special_price', 'text', array(
						'name'=>'warehouse_special_price',
						'class'=>'requried-entry',
						'value'=>''
				),'special_to_date');
				
				$form->getElement('warehouse_special_price')->setRenderer(
					$layout->createBlock('ksprice/adminhtml_catalog_product_edit_tab_price_special')
				);
				
				break;
			}
		}
		
		//return $this;
	}
	
	
	/*
	* save warehouse pricing
	*/
    public function saveWarehousePricing($observer)
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
					if(isset($data['product']['warehouse_price']) && is_array($data['product']['warehouse_price']) && count($data['product']['warehouse_price'])){
						
						$table_name = Mage::getSingleton('core/resource')->getTableName('ksprice/price');
						
						foreach($data['product']['warehouse_price'] as $_cp){
							
							try {
								$transaction->beginTransaction();
								
								if($_cp['delete']){
									$transaction->query("DELETE FROM `".$table_name."` WHERE `location_id` = '$_cp[warehouse_id]' AND `product_id` = '".$product->getData('entity_id')."'");
								}else{
								
									$transaction->query("INSERT INTO `".$table_name."` (
									  `product_id`,
									  `location_id`,
									  `price`
									) 
									VALUES
									  (
										'".$product->getData('entity_id')."',
										'$_cp[warehouse_id]',
										'$_cp[price]'
									  ) ON DUPLICATE KEY UPDATE `price` = '$_cp[price]'");
								}
								$transaction->commit();
							} catch (Exception $e) {
								$transaction->rollBack(); 
							}
		
						}
					}

					//warehouse special price
					if(isset($data['product']['warehouse_special_price']) && is_array($data['product']['warehouse_special_price']) && count($data['product']['warehouse_special_price'])){

						$table_name = Mage::getSingleton('core/resource')->getTableName('ksprice/special');

						foreach($data['product']['warehouse_special_price'] as $_cp){
							
							try {
								$transaction->beginTransaction();
								
								if($_cp['delete']){
									$transaction->query("DELETE FROM `".$table_name."` WHERE `location_id` = '$_cp[warehouse_id]' AND `product_id` = '".$product->getData('entity_id')."'");
								}else{
									
									$from  = trim($_cp['from']);
									$to  = trim($_cp['to']);
									
									$transaction->query("INSERT INTO `".$table_name."` (
									  `product_id`,
									  `location_id`,
									  `date_from`,
									  `date_to`,
									  `price`
									) 
									VALUES
									  (
										'".$product->getData('entity_id')."',
										'$_cp[warehouse_id]',
										".($from ? "'".$from."'":"NULL").",
										".($to ? "'".$to."'":"NULL").",
										'$_cp[price]'
									  ) ON DUPLICATE KEY UPDATE `date_from` = ".($from ? "'".$from."'":"NULL").", `date_to` = ".($to ? "'".$to."'":"NULL").", `price` = '$_cp[price]'");
								}
								$transaction->commit();
							} catch (Exception $e) {
								$transaction->rollBack(); 
							}
		
						}
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
		if($warehouse_id = Mage::helper('kswarehouse')->isWarehouseMode()){
			$warehouse = Mage::getModel('ks_warehouse/location')->load($warehouse_id);
			$product->setWarehouse($warehouse);
		}
			
	}
	
	/*
	* apply club price
	*/
	public function applyQuoteLocation($observer){
		if($warehouse_id = Mage::helper('kswarehouse')->isWarehouseMode()){
			$quote = Mage::getSingleton('checkout/cart')->getQuote();
			$quote->setLocationId($warehouse_id);
		}
	}
	
	
	public function setWarehouseToProducts($observer){
		if($warehouse_id = Mage::helper('kswarehouse')->isWarehouseMode()){
			$productCollection = $observer->getEvent()->getCollection();
			$warehouse = Mage::getModel('ks_warehouse/location')->load($warehouse_id);
			foreach ($productCollection as $product) {
				$product->setWarehouse($warehouse);
				if(Mage::app()->getStore()->isAdmin())
					$product->setPrice($product->getPrice());
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
	* remove checkout session
	*/
	public function orderPlaceAfter($observer)
	{
		$order = $observer->getEvent()->getOrder();

		try{
			//clear session
			$session = Mage::getSingleton('checkout/session');	
			$session->setBaseClubCityId(null) ;
			$session->setBaseClubId(null) ;
			$session->setBaseClubCustomerFirstname(null) ;
			$session->setBaseClubCustomerLastname(null) ;
			$session->setBaseClubCustomerEmail(null) ;
			$session->setPtProduct(null) ;
			$session->setPtTheme(null) ;
			$session->setPtLevel(null) ;
			$session->setPtWorkouts(null) ;
			$session->setMembershipProduct(null);
			$session->setRequiredCode(null);
			$session->setAcceptedTos(null);		
				
			$abandoned = Mage::getModel('club/abandoned_lead')->load($order->getQuoteId(),'quote_id');
			$abandoned->delete();
		}catch(Exception $e){
		   Mage::logException($e);
		}

	}
	
	
	/*
	* create membership after invoice
	*/
	public function createCfitMembership($observer){
		try{
			$order = $observer->getEvent()->getInvoice()->getOrder();
			Mage::helper('club/membership')->sendTransactionalCreateMembership($order);
			
		} catch(Exception $e){
			Mage::logException($e);
		}
		
	}
		
}
