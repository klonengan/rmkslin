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
 * @package     KS_Brand
 * @copyright   Copyright (c) 2014 Kemana Services (http://www.kemanaservices.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Installer to create table for KS_Brand
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

$installer = $this;
$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('banner/type')}`;  
    CREATE TABLE `{$installer->getTable('banner/type')}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
        `name` varchar(50) DEFAULT NULL COMMENT 'Brand Type',
        `width` int(10) DEFAULT NULL COMMENT 'Banner Width(Pixel)',
        `height` int(10) DEFAULT NULL COMMENT 'Banner Height(Pixel)',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8 COMMENT='KS Banner Type Table';
");

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('banner/list')}`;  
    CREATE TABLE `{$installer->getTable('banner/list')}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
        `type_id` int(10) DEFAULT NULL COMMENT 'Banner Type Id',
        `image` varchar(255) DEFAULT NULL COMMENT 'Banner Image',
        `sort_order` int(10) DEFAULT NULL COMMENT 'Sort Order',
        `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Creation Time',
        `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT 'Update Time',
        `admin_id` int(10) DEFAULT NULL COMMENT 'Admin Id',
        `active` smallint(5) DEFAULT '1' COMMENT 'Is Active',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8 COMMENT='KS Banner Type Table';
");

$installer->endSetup();