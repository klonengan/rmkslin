<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 1/5/2016
 * Time: 10:09 AM
 * Switch Website Data helper
 */

class KS_WebsiteSwitcher_Helper_Data extends Mage_Core_Helper_Abstract {
    /*
        Set Default Website
    */
    public function set_default_website($id)
    {
        $data = array( 'is_default'=> 1 );
        $model = Mage::getModel('mynews/mynews')->load($id)->addData($data);
        try {
            $model->setId($id)->save();
            header("Refresh:0");
            //echo "Data updated successfully.";
        } catch (Exception $e){
            echo $e->getMessage();
        }
    }
}