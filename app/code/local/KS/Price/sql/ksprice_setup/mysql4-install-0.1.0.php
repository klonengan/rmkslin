<?php
 
$installer = $this;

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
$installer->run("CREATE TABLE `ks_product_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `price` double(14,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `U` (`product_id`,`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `ks_product_special_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `price` double(14,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `U` (`product_id`,`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `sales_flat_order` ADD COLUMN `location_id` INT(11) NULL;
ALTER TABLE `sales_flat_quote` ADD COLUMN `location_id` INT(11) NULL;

");
$installer->endSetup();