<?php
class KS_Price_Model_Mysql4_Price_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('ksprice/price');
    }
}