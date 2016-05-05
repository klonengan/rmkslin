<?php
class KS_Warehouse_Model_User extends Mage_Admin_Model_User 
{
	protected function _afterSave()
	{
		 $transaction = Mage::getSingleton('core/resource')->getConnection('core_write');
		 $table_name = Mage::getSingleton('core/resource')->getTableName('ks_warehouse/location_admin');
		
		 /*----- warehouses ------*/
		 $unset = array();
		 $uset_warehouses = explode(",", Mage::app()->getRequest()->getPost('unset_warehouse') );
		 if(count($uset_warehouses)){
		 	foreach($uset_warehouses as $v){
		 	  $v = trim($v);
		 	  if($v)
		 			$unset[] = "'".$v."'";
		 	}
		 }
	   
	    if(count($unset)){
		 	try {
		 		$transaction->beginTransaction();
		 		$transaction->query('DELETE FROM `'.$table_name.'` WHERE user_id = "'.$this->getData('user_id').'"  and location_id IN ('.implode(",", $unset).')');
		 		$transaction->commit();
		 	} catch (Exception $e) {
		 		$transaction->rollBack(); 
		 	}		   
	    }
	   
		 $sett = array();
		 $sett_warehouses = explode(",", Mage::app()->getRequest()->getPost('set_warehouse') );
		 if(count($sett_warehouses)){
		 	foreach($sett_warehouses as $v){
		 	  $v = trim($v);
		 	  if($v)
		 			$sett[] = $v;
		 	}
		 }
	   
	   
		 if (count($sett)) {
		    foreach($sett as $_club){
		 	   $warehouseadmin = Mage::getModel('ks_warehouse/location_admin');
		 	   $warehouseadmin->setLocationId($_club);
		 	   $warehouseadmin->setUserId($this->getData('user_id'));
		 	   $warehouseadmin->save();
		    }
		 }
		 	 
		return $this;
	}
}
