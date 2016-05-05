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
 * @package     KS_KSDirectory
 * @copyright   Copyright (c) 2014 Kemana Services (http://www.kemanaservices.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Installer to create table for KS Directory
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

$installer = $this;
$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('ksdirectory/grid')}`;  
    CREATE TABLE `{$installer->getTable('ksdirectory/grid')}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `country_code` varchar(2) DEFAULT NULL COMMENT 'Kode Negara',
        `province_code` varchar(5) DEFAULT NULL COMMENT 'Kode Provinsi',
        `province_name` varchar(100) DEFAULT NULL COMMENT 'Nama Provinsi',
        `regency_code` varchar(8) DEFAULT NULL COMMENT 'Kode Kabupaten/Kota',
        `regency_name` varchar(100) DEFAULT NULL COMMENT 'Nama Kabupaten/Kota',
        `subdistrict_code` varchar(11) DEFAULT NULL COMMENT 'Kode Kecamatan',
        `subdistrict_name` varchar(100) DEFAULT NULL COMMENT 'Nama Kecamatan',
        `village_code` varchar(14) DEFAULT NULL COMMENT 'Kode Kelurahan/Desa',
        `village_name` varchar(100) DEFAULT NULL COMMENT 'Nama Kelurahan/Desa',
        `postcode` varchar(5) DEFAULT NULL COMMENT 'Kode Pos',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8 COMMENT='KS Directory List for Grid';
");

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('ksdirectory/province')}`;  
    CREATE TABLE `{$installer->getTable('ksdirectory/province')}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `country_code` varchar(2) DEFAULT NULL COMMENT 'Kode Negara',
        `province_code` varchar(5) DEFAULT NULL COMMENT 'Kode Provinsi',
        `province_name` varchar(100) DEFAULT NULL COMMENT 'Nama Provinsi',
        `active` smallint(5) DEFAULT NULL COMMENT 'Active',
        PRIMARY KEY (`id`),
        UNIQUE KEY `province_code` (`province_code`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8 COMMENT='KS Directory Province List';
");

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('ksdirectory/regency')}`;  
    CREATE TABLE `{$installer->getTable('ksdirectory/regency')}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `province_code` varchar(5) DEFAULT NULL COMMENT 'Kode Provinsi',
        `regency_code` varchar(8) DEFAULT NULL COMMENT 'Kode Kabupaten/Kota',
        `regency_name` varchar(100) DEFAULT NULL COMMENT 'Nama Kabupaten/Kota',
        `active` smallint(5) DEFAULT NULL COMMENT 'Active',
        PRIMARY KEY (`id`),
        UNIQUE KEY `regency_code` (`regency_code`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8 COMMENT='KS Directory Regency List';
");

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('ksdirectory/subdistrict')}`;  
    CREATE TABLE `{$installer->getTable('ksdirectory/subdistrict')}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `regency_code` varchar(8) DEFAULT NULL COMMENT 'Kode Kabupaten/Kota',
        `subdistrict_code` varchar(11) DEFAULT NULL COMMENT 'Kode Kecamatan',
        `subdistrict_name` varchar(100) DEFAULT NULL COMMENT 'Nama Kecamatan',
        `active` smallint(5) DEFAULT NULL COMMENT 'Active',
        PRIMARY KEY (`id`),
        UNIQUE KEY `subdistrict_code` (`subdistrict_code`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8 COMMENT='KS Directory Sub-district List';
");

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('ksdirectory/village')}`;  
    CREATE TABLE `{$installer->getTable('ksdirectory/village')}` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `subdistrict_code` varchar(11) DEFAULT NULL COMMENT 'Kode Kecamatan',
        `village_code` varchar(14) DEFAULT NULL COMMENT 'Kode Kelurahan/Desa',
        `village_name` varchar(100) DEFAULT NULL COMMENT 'Nama Kelurahan/Desa',
        `postcode` varchar(5) DEFAULT NULL COMMENT 'Kode Pos',
        `active` smallint(5) DEFAULT NULL COMMENT 'Active',
        PRIMARY KEY (`id`),
        UNIQUE KEY `village_code` (`village_code`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8 COMMENT='KS Directory Village List';
");

$installer->endSetup();