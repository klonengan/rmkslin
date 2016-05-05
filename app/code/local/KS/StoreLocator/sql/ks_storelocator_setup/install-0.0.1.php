<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    KS
 * @package     KS_StoreLocator
 * @copyright   Copyright (c) 2014 Kemana Services (http://www.kemanaservices.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Installer to create table ks_store_locator
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

$installer = $this;
$installer->startSetup();
$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('storelocator/store')}`;  
    CREATE TABLE `{$installer->getTable('storelocator/store')}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
        `province` varchar(255) DEFAULT NULL COMMENT 'Province Name',
        `city` varchar(255) DEFAULT NULL COMMENT 'City Name',
        `store_name` varchar(255) DEFAULT NULL COMMENT 'Shop Name',
        `store_address1` varchar(255) DEFAULT NULL COMMENT 'Shop Address Line 1',
        `store_address2` varchar(255) DEFAULT NULL COMMENT 'Shop Address Line 2',
        `store_phone` varchar(255) DEFAULT NULL COMMENT 'Shop Phone',
        `store_fax` varchar(255) DEFAULT NULL COMMENT 'Shop Fax',
        `store_email` varchar(150) DEFAULT NULL COMMENT 'Shop Email',
        `store_operations` varchar(100) DEFAULT NULL COMMENT 'Store Operations',
        `google_latitude` varchar(50) DEFAULT NULL COMMENT 'Google Map Latitude',
        `google_longitude` varchar(50) DEFAULT NULL COMMENT 'Google Map Longitude',
        `is_hotspot` smallint(6) DEFAULT NULL COMMENT 'Is Hotspot',
        `is_active` smallint(6) DEFAULT '0' COMMENT 'Is Active',
        /*`super_hide` smallint(6) DEFAULT '0' COMMENT 'Super Hide',*/
        `created_at` datetime DEFAULT NULL COMMENT 'Created Date',
        `updated_at` datetime DEFAULT NULL COMMENT 'Updated Date',
        `history` text COMMENT 'History of Update',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT  CHARSET=utf8 COMMENT='KS Store Locator';
");

$installer->endSetup();