<?php
 
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();	
$installer->run("
DROP TABLE IF EXISTS `".$installer->getTable('ks_warehouse/stock')."`;
CREATE TABLE `".$installer->getTable('ks_warehouse/stock')."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `qty` double(14,4) DEFAULT NULL,
  `is_instock` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `U` (`product_id`,`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
");
$installer->endSetup();