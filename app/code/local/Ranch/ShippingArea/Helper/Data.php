<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 3/1/2016
 * Time: 2:34 PM
 */

class Ranch_ShippingArea_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getShippingDistance($zipcode)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT zipcode FROM ks_shipping_area WHERE zipcode = '.$zipcode.' AND distance < 5.5 LIMIT 1';
        $results = $readConnection->fetchCol($query);
        if($results) return true;
    }

}