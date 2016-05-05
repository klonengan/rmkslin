<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 1/14/2016
 * Time: 8:34 AM
 */
class Ranch_LandingPage_Model_Mysql4_Store_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct(){
        parent::_construct();
        $this->_init('landingpage/store');
    }

}