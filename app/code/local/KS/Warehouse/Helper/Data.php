<?php
class KS_Warehouse_Helper_Data extends Mage_Core_Helper_Abstract
{
    CONST SUPER_ADMIN_ROLE_ID = 1;
    
	public function isAdminAllowWarehouse(){
		//get role_id first
		$role_id = (int)implode('', Mage::getSingleton('admin/session')->getUser()->getRoles());

        // if Super Admin -> Role ID = 1
        if($role_id == self::SUPER_ADMIN_ROLE_ID)
            return true;

		//get current ids
		$user_id = Mage::getSingleton('admin/session')->getUser()->getUserId();
		
		//get club collection
		$collection = Mage::getModel('ks_warehouse/location_admin')->getCollection();
		$collection->addFieldToFilter('user_id', array('eq' => $user_id));

		if($collection->getSize()){
			$warehouses = array();
			foreach($collection as $w){
				$warehouses[] = $w->getLocationId();
			}
			
			return $warehouses;
		}

		return false;
	}
	
	public function isWarehouseMode(){
		if(!Mage::app()->getStore()->isAdmin() && isset($_COOKIE[KS_Price_Helper_Data::COOKIE_NAME]) && $warehouse_id = (int)$_COOKIE[KS_Price_Helper_Data::COOKIE_NAME])
			return $warehouse_id;
		if(Mage::app()->getStore()->isAdmin() && $warehouse_id = Mage::registry("ks_backend_warehouse"))
			return $warehouse_id;
		return 0;
	}
}