<?php
 
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();	
$installer->run("
DROP TABLE IF EXISTS `".$installer->getTable('ks_warehouse/location_admin')."`;
CREATE TABLE `".$installer->getTable('ks_warehouse/location_admin')."` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `location_id` int(10) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
");
$installer->endSetup();