<?php
class KS_Price_Model_Mysql4_Special extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('ksprice/special', 'id');
    }
}