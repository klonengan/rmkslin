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
 * KS_KSDirectory_Model_Mysql4_Grid
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */


class KS_KSDirectory_Model_Mysql4_Grid extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('ksdirectory/grid', 'id');
    }

    public function updateProvinceName($provinceCode, $newProvinceName)
    {
        $_model = Mage::getModel('ksdirectory/grid')->getCollection()->addFieldToFilter('province_code', array('eq' => $provinceCode));
        if($_model->getSize() > 0){
            $writeDb = $this->_getWriteAdapter();
            $gridTable = $this->getTable('ksdirectory/grid');
            try {
                foreach ($_model as $grid) {
                    $gridId = $grid->getId();
                    $data = array(
                        'province_name' => $newProvinceName
                    );
                    $condition = ' id = '.$gridId;
                    $writeDb->update($gridTable, $data, $condition);
                }
                return true;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        return false;
    }

    public function updateProvince($oldProvinceCode, $newProvinceCode, $newProvinceName)
    {
        $_model = Mage::getModel('ksdirectory/grid')->getCollection()->addFieldToFilter('province_code', array('eq' => $oldProvinceCode));
    	if($_model->getSize() > 0){

            $writeDb = $this->_getWriteAdapter();
            $gridTable = $this->getTable('ksdirectory/grid');

            // if old country not equal current country
    		if($oldProvinceCode != $newProvinceCode){
                try {
                    foreach ($_model as $grid) {
                        $gridId = $grid->getId();
                        $newRegencyCode = str_replace($oldProvinceCode, $newProvinceCode, $grid->getRegencyCode());
                        $newSubdistrictCode = str_replace($oldProvinceCode, $newProvinceCode, $grid->getSubdistrictCode());
                        $newVillageCode = str_replace($oldProvinceCode, $newProvinceCode, $grid->getVillageCode());
                        $newCountryCode = strtoupper(substr($newRegencyCode, 0, 2));
                       
                        $data = array(
                            'country_code' => $newCountryCode,
                            'province_code' => $newProvinceCode,
                            'province_name' => $newProvinceName,
                            'regency_code' => $newRegencyCode,
                            'subdistrict_code' => $newSubdistrictCode,
                            'village_code' => $newVillageCode
                        );
                        $condition = ' id = '.$gridId;
                        $writeDb->update($gridTable, $data, $condition);
                    }

                    return true;
                }
                catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
    		}
    	}
    	return false;
    }

    public function updateRegencyName($regencyCode, $newRegencyName)
    {
        $_model = Mage::getModel('ksdirectory/grid')->getCollection()->addFieldToFilter('regency_code', array('eq' => $regencyCode));
        if($_model->getSize() > 0){
            $writeDb = $this->_getWriteAdapter();
            $gridTable = $this->getTable('ksdirectory/grid');
            try {
                foreach ($_model as $grid) {
                    $gridId = $grid->getId();
                    $data = array(
                        'regency_name' => $newRegencyName
                    );
                    $condition = ' id = '.$gridId;
                    $writeDb->update($gridTable, $data, $condition);
                }
                return true;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        return false;
    }

    public function updateRegency($oldRegencyCode, $newRegencyCode, $newRegencyName)
    {
        $_model = Mage::getModel('ksdirectory/grid')->getCollection()->addFieldToFilter('regency_code', array('eq' => $oldRegencyCode));
        if($_model->getSize() > 0){

            $writeDb = $this->_getWriteAdapter();
            $gridTable = $this->getTable('ksdirectory/grid');

            // if old province not equal current province
            if($oldRegencyCode != $newRegencyCode){
                try {
                    $newCountryCode = strtoupper(substr($newRegencyCode, 0, 2));
                    $newProvinceCode = strtoupper(substr($newRegencyCode, 0, 5));
                    $newProvinceName = Mage::getModel('ksdirectory/province')->getCollection()->addFieldToFilter('province_code', array('eq' => $newProvinceCode))->getFirstItem()->getProvinceName();
                    foreach ($_model as $grid) {
                        $gridId = $grid->getId();
                        $newSubdistrictCode = str_replace($oldRegencyCode, $newRegencyCode, $grid->getSubdistrictCode());
                        $newVillageCode = str_replace($oldRegencyCode, $newRegencyCode, $grid->getVillageCode());

                        $data = array(
                            'country_code' => $newCountryCode,
                            'province_code' => $newProvinceCode,
                            'province_name' => $newProvinceName,
                            'regency_code' => $newRegencyCode,
                            'regency_name' => $newRegencyName,
                            'subdistrict_code' => $newSubdistrictCode,
                            'village_code' => $newVillageCode
                        );
                        $condition = ' id = '.$gridId;
                        $writeDb->update($gridTable, $data, $condition);
                    }

                    return true;
                }
                catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        return false;
    }

    public function updateSubdistrictName($subdistrictCode, $newSubdistrictName)
    {
        $_model = Mage::getModel('ksdirectory/grid')->getCollection()->addFieldToFilter('subdistrict_code', array('eq' => $subdistrictCode));
        if($_model->getSize() > 0){
            $writeDb = $this->_getWriteAdapter();
            $gridTable = $this->getTable('ksdirectory/grid');
            try {
                foreach ($_model as $grid) {
                    $gridId = $grid->getId();
                    $data = array(
                        'subdistrict_name' => $newSubdistrictName
                    );
                    $condition = ' id = '.$gridId;
                    $writeDb->update($gridTable, $data, $condition);
                }
                return true;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        return false;
    }

    public function updateSubdistrict($oldSubdistrictCode, $newSubdistrictCode, $newSubdistrictName)
    {
        $_model = Mage::getModel('ksdirectory/grid')->getCollection()->addFieldToFilter('subdistrict_code', array('eq' => $oldSubdistrictCode));
        if($_model->getSize() > 0){

            $writeDb = $this->_getWriteAdapter();
            $gridTable = $this->getTable('ksdirectory/grid');

            // if old province not equal current province
            if($oldSubdistrictCode != $newSubdistrictCode){
                try {
                    $newCountryCode = strtoupper(substr($newSubdistrictCode, 0, 2));
                    $newProvinceCode = strtoupper(substr($newSubdistrictCode, 0, 5));
                    $newProvinceName = Mage::getModel('ksdirectory/province')->getCollection()->addFieldToFilter('province_code', array('eq' => $newProvinceCode))->getFirstItem()->getProvinceName();
                    $newRegencyCode = strtoupper(substr($newSubdistrictCode, 0, 8));
                    $newRegencyName = Mage::getModel('ksdirectory/regency')->getCollection()->addFieldToFilter('regency_code', array('eq' => $newRegencyCode))->getFirstItem()->getRegencyName();
                    foreach ($_model as $grid) {
                        $gridId = $grid->getId();
                        $newVillageCode = str_replace($oldSubdistrictCode, $newSubdistrictCode, $grid->getVillageCode());
                        
                        $data = array(
                            'country_code' => $newCountryCode,
                            'province_code' => $newProvinceCode,
                            'province_name' => $newProvinceName,
                            'regency_code' => $newRegencyCode,
                            'regency_name' => $newRegencyName,
                            'subdistrict_code' => $newSubdistrictCode,
                            'subdistrict_name' => $newSubdistrictName,
                            'village_code' => $newVillageCode
                        );
                        $condition = ' id = '.$gridId;
                        $writeDb->update($gridTable, $data, $condition);
                    }

                    return true;
                }
                catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        return false;
    }

    public function updateVillageName($villageCode, $newVillageName, $newPostcode)
    {
        $_model = Mage::getModel('ksdirectory/grid')->getCollection()->addFieldToFilter('village_code', array('eq' => $villageCode));
        if($_model->getSize() > 0){
            $writeDb = $this->_getWriteAdapter();
            $gridTable = $this->getTable('ksdirectory/grid');
            try {
                foreach ($_model as $grid) {
                    $gridId = $grid->getId();
                    $data = array(
                        'village_name' => $newVillageName,
                        'postcode' => $newPostcode
                    );
                    $condition = ' id = '.$gridId;
                    $writeDb->update($gridTable, $data, $condition);
                }
                return true;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        return false;
    }

    public function updateVillage($oldVillageCode, $newVillageCode, $newVillageName, $newPostcode)
    {
        $_model = Mage::getModel('ksdirectory/grid')->getCollection()->addFieldToFilter('village_code', array('eq' => $oldVillageCode));
        if($_model->getSize() > 0){

            $writeDb = $this->_getWriteAdapter();
            $gridTable = $this->getTable('ksdirectory/grid');

            // if old province not equal current province
            if($oldVillageCode != $newVillageCode){
                try {
                    $newCountryCode = strtoupper(substr($newVillageCode, 0, 2));
                    $newProvinceCode = strtoupper(substr($newVillageCode, 0, 5));
                    $newProvinceName = Mage::getModel('ksdirectory/province')->getCollection()->addFieldToFilter('province_code', array('eq' => $newProvinceCode))->getFirstItem()->getProvinceName();
                    $newRegencyCode = strtoupper(substr($newVillageCode, 0, 8));
                    $newRegencyName = Mage::getModel('ksdirectory/regency')->getCollection()->addFieldToFilter('regency_code', array('eq' => $newRegencyCode))->getFirstItem()->getRegencyName();
                    $newSubdistrictCode = strtoupper(substr($newVillageCode, 0, 11));
                    $newSubdistrictName = Mage::getModel('ksdirectory/subdistrict')->getCollection()->addFieldToFilter('subdistrict_code', array('eq' => $newSubdistrictCode))->getFirstItem()->getSubdistrictName();
                    foreach ($_model as $grid) {
                        $gridId = $grid->getId();
                        
                        $data = array(
                            'country_code' => $newCountryCode,
                            'province_code' => $newProvinceCode,
                            'province_name' => $newProvinceName,
                            'regency_code' => $newRegencyCode,
                            'regency_name' => $newRegencyName,
                            'subdistrict_code' => $newSubdistrictCode,
                            'subdistrict_name' => $newSubdistrictName,
                            'village_code' => $newVillageCode,
                            'village_name' => $newVillageName,
                            'postcode' => $newPostcode
                        );
                        $condition = ' id = '.$gridId;
                        $writeDb->update($gridTable, $data, $condition);
                    }

                    return true;
                }
                catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        return false;
    }

}