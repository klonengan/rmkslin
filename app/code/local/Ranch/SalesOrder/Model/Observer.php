<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 3/27/2016
 * Time: 7:40 PM
 */
class Ranch_SalesOrder_Model_Observer
{
    const PICKER = "Picker";

    public function coreBlockAbstractToHtmlBefore(Varien_Event_Observer $observer)
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getEvent()->getBlock();
        if ($block->getId() == 'sales_order_grid') {

            //add new column: payment method
            $block->addColumnAfter('status', array(
                'header' => Mage::helper('sales')->__('Shipping'),
                'type' => 'text',
                'index' => 'shipping_desc',
                'filter_index' => 'main_table.shipping_desc',
            ), "shipping_name");

            $block->sortColumnsByOrder();
        }
    }

    public function salesOrderGridCollectionLoadBefore(Varien_Event_Observer $observer)
    {
        $admin_user_session = Mage::getSingleton('admin/session');
        $adminuserId = $admin_user_session->getUser()->getUserId();
        $role_data = Mage::getModel('admin/user')->load($adminuserId)->getRole()->getData('role_name');
        if($role_data == self::PICKER){
            $pickerStatus = array('pending'=>'Pending');
        }else{

        }
        $collection = $observer->getOrderGridCollection();
        $select = $collection->getSelect();
        $select->joinLeft( array('sfo'=>'sales_flat_order'),'main_table.entity_id=sfo.entity_id', array("shipping_desc"=>"shipping_description"));
    }

}