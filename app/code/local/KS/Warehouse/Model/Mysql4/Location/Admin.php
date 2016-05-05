<?php
class KS_Warehouse_Model_Mysql4_Location_Admin extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('ks_warehouse/location_admin', 'id');
    }
}