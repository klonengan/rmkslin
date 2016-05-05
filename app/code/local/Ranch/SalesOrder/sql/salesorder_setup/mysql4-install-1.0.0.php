<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 3/28/2016
 * Time: 1:33 PM
 */
//die('installer order reference ');
$installer = $this;
$installer->startSetup();

$configs = array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'Order Reference Number'
);

$order_ref = 'order_reference';

//$installer->getConnection()->addColumn($installer->getTable('sales/quote'),$order_ref, $configs);
$installer->getConnection()->addColumn($installer->getTable('sales/order'),$order_ref, $configs);
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'),$order_ref, $configs);
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo'),$order_ref, $configs);
$installer->getConnection()->addColumn($installer->getTable('sales/shipment'),$order_ref, $configs);

$installer->endSetup();