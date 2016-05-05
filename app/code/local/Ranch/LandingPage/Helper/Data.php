<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 1/5/2016
 * Time: 10:09 AM
 * Switch Website Data helper
 */

class Ranch_LandingPage_Helper_Data extends Mage_Core_Helper_Abstract {

    public function print_rr($array)
    {
        echo "<pre>";
        $result  = print_r($array);
        echo "</pre>";
        return $result;
    }


}