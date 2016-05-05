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
 * KS_KSDirectory_Model_Mysql4_Regency
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */


class KS_KSDirectory_Model_Mysql4_Regency extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('ksdirectory/regency', 'id');
    }

    public function updateProvinceCode($oldProvinceCode, $newProvinceCode)
    {
    	$_model = Mage::getModel('ksdirectory/regency')->getCollection()->addCustomFilter('province_code', 'eq', $oldProvinceCode);
    	if($_model->getSize() > 0){
    		
    		$writeDb = $this->_getWriteAdapter();
	    	$regencyTable = $this->getTable('ksdirectory/regency');

	    	if($oldProvinceCode != $newProvinceCode){
	    		try {
		    		foreach ($_model as $regency) {
		                $regencyId = $regency->getId();
		                $newRegencyCode = str_replace($oldProvinceCode, $newProvinceCode, $regency->getRegencyCode());
		               
		                $data = array(
		                    'province_code' => $newProvinceCode,
		                    'regency_code' => $newRegencyCode
		                );
		                $condition = ' id = '.$regencyId;
		                $writeDb->update($regencyTable, $data, $condition);
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