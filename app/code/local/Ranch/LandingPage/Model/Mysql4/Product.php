<?php
class Ranch_LandingPage_Model_Mysql4_Product extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('landingpage/product','id');
    }

}