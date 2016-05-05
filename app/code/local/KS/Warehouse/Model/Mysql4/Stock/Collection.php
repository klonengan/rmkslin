<?php

class KS_Warehouse_Model_Mysql4_Stock_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('ks_warehouse/stock');
    }

}