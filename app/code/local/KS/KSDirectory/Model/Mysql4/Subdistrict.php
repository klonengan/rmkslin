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
 * KS_KSDirectory_Model_Mysql4_Subdistrict
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */


class KS_KSDirectory_Model_Mysql4_Subdistrict extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('ksdirectory/subdistrict', 'id');
    }

    public function updateProvinceCode($oldProvinceCode, $newProvinceCode)
    {
    	$_model = Mage::getModel('ksdirectory/subdistrict')->getCollection()->addFieldToFilter('regency_code', array('like' => $oldProvinceCode.'%'));
    	if($_model->getSize() > 0){
    		
    		$writeDb = $this->_getWriteAdapter();
	    	$subdistrictTable = $this->getTable('ksdirectory/subdistrict');

	    	if($oldProvinceCode != $newProvinceCode){
	    		try {
		    		foreach ($_model as $subdistrict) {
		                $subdistrictId = $subdistrict->getId();
		                $newRegencyCode = str_replace($oldProvinceCode, $newProvinceCode, $subdistrict->getRegencyCode());
		                $newSubdistrictCode = str_replace($oldProvinceCode, $newProvinceCode, $subdistrict->getSubdistrictCode());
		               
		                $data = array(
		                    'regency_code' => $newRegencyCode,
		                    'subdistrict_code' => $newSubdistrictCode
		                );
		                $condition = ' id = '.$subdistrictId;
		                $writeDb->update($subdistrictTable, $data, $condition);
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

    public function updateRegencyCode($oldRegencyCode, $newRegencyCode)
    {
    	$_model = Mage::getModel('ksdirectory/subdistrict')->getCollection()->addCustomFilter('regency_code', 'eq', $oldRegencyCode);
    	if($_model->getSize() > 0){
    		
    		$writeDb = $this->_getWriteAdapter();
	    	$subdistrictTable = $this->getTable('ksdirectory/subdistrict');

	    	if($oldRegencyCode != $newRegencyCode){
	    		try {
		    		foreach ($_model as $subdistrict) {
		                $subdistrictId = $subdistrict->getId();
		                $newSubdistrictCode = str_replace($oldRegencyCode, $newRegencyCode, $subdistrict->getSubdistrictCode());
		               
		                $data = array(
		                    'regency_code' => $newRegencyCode,
		                    'subdistrict_code' => $newSubdistrictCode
		                );
		                $condition = ' id = '.$subdistrictId;
		                $writeDb->update($subdistrictTable, $data, $condition);
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