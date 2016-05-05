<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 1/5/2016
 * Time: 10:55 AM
 */
class KS_WebsiteSwitcher_IndexController extends Mage_Core_Controller_Front_Action  {

    public function indexAction(){
        //set cookies
        $storeid = $this->getRequest()->getParam('storeid');
        if($storeid) {
            setcookie(KS_Price_Helper_Data::COOKIE_NAME, $storeid, time() + KS_Price_Helper_Data::COOKIE_TIME_LENGTH, "/");
            // clear shopping cart
            $cartHelper = Mage::helper('checkout/cart');
            $items = $cartHelper->getCart()->getItems();
            foreach ($items as $item) {
                $itemId = $item->getItemId();
                //Remove items, one by one
                $cartHelper->getCart()->removeItem($itemId)->save();
            }
            //Redirect
            echo true;
        }else{
            echo false;
        }
    }

}