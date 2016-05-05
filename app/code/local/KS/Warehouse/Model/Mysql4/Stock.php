<?php
class KS_Warehouse_Model_Mysql4_Stock extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('ks_warehouse/stock', 'id');
    }
}