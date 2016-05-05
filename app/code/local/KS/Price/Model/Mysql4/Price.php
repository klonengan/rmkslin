<?php
class KS_Price_Model_Mysql4_Price extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('ksprice/price', 'id');
    }
}