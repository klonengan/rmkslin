<?php
class KS_Warehouse_Model_Location_Admin extends Mage_Core_Model_Abstract {

	protected function _construct(){
		parent::_construct();
		$this->_init( 'ks_warehouse/location_admin' );
	}
}
