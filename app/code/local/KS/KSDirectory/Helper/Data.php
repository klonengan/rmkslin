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
 * KS_KSDirectory_Helper_Data
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */
class KS_KSDirectory_Helper_Data extends Mage_Core_Helper_Abstract
{
	const DIRECTORY_PROVINCE_ENABLE = 'ksdirectory/province/active';
	const DIRECTORY_REGENCY_ENABLE = 'ksdirectory/regency/active';
	const DIRECTORY_SUBDISTRICT_ENABLE = 'ksdirectory/subdistrict/active';
	const DIRECTORY_VILLAGE_ENABLE = 'ksdirectory/village/active';

	public function isProvinceEnable(){
		return Mage::getStoreConfig(self::DIRECTORY_PROVINCE_ENABLE);
	}

	public function isRegencyEnable(){
		return Mage::getStoreConfig(self::DIRECTORY_REGENCY_ENABLE);
	}

	public function isSubdistrictEnable(){
		return Mage::getStoreConfig(self::DIRECTORY_SUBDISTRICT_ENABLE);
	}

	public function isVillageEnable(){
		return Mage::getStoreConfig(self::DIRECTORY_VILLAGE_ENABLE);
	}
}