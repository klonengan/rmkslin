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
 * Source Model KS_KSDirectory_Model_Source_Subdistrict
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Model_Source_Subdistrict
{
	public function toOptionArray($type = 0, $filterByRegencyCode = '')
	{
		$options = array();
		$collection = Mage::getModel('ksdirectory/subdistrict')->getCollection();

		if($type == 0){
			$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('subdistrict_name')->order('subdistrict_name', 'ASC');
			if($collection->getData()){
				foreach ($collection->getData() as $key => $value) {
					$options[$value['subdistrict_name']] = $value['subdistrict_name'];
				}
			}
		}
		elseif($type == 1){
			$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('subdistrict_code')->columns('subdistrict_name')->order('subdistrict_name', 'ASC');
			if($collection->getData()){
				$options[''] = '-- Please Select --';
				foreach ($collection->getData() as $key => $value) {
					$options[$value['subdistrict_code']] = $value['subdistrict_name'];
				}
			}
		}
		elseif($type == 2)
		{
			if($filterByRegencyCode != ''){
				$collection->addFieldToFilter('regency_code', $filterByRegencyCode);

				$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('subdistrict_code')->columns('subdistrict_name')->order('subdistrict_name', 'ASC');

				if($collection->getData()){
					$options[''] = '-- Please Select --';
					foreach ($collection->getData() as $value) {
						$options[$value['subdistrict_code']] = $value['subdistrict_name'];
					}
				}
			}

		}
		elseif($type == 3)
		{
			$collection = Mage::getModel('ksdirectory/grid')->getCollection();
			if($filterByRegencyCode != '') $collection->addFieldToFilter('regency_code', $filterByRegencyCode);
			$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('province_name')->columns('regency_name')->columns('subdistrict_name')->columns('subdistrict_code');
			$collection->getSelect()->group('subdistrict_code')->order('province_name', 'ASC')->order('regency_name', 'ASC')->order('subdistrict_name', 'ASC');

			$countries = array();
			if($collection->getData()){
				foreach ($collection->getData() as $country) {
	                $countries['Indonesia'][$country['province_name']][$country['regency_name']][$country['subdistrict_name']] = $country['subdistrict_code'];
	            }

	            foreach ($countries as $country => $_provinces){
	            	foreach ($_provinces as $_province => $_regions) {
	            		$label = '';
	            		foreach ($_regions as $_region => $_subdistricts) {
	            			$subdistricts = array();
	            			foreach ($_subdistricts as $key => $val) {
		            			$subdistricts[] = array('label' => $key, 'value' => $val);
		            		}
		            		$label = $country.' / '.$_province.' / '.$_region;
	            			$options[] = array('label' => $label, 'value' => $subdistricts);
	            		}
	            		
	            	}
	            }
	        } 
		}
		
		return $options;
	}
}
