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
 * Source Model KS_KSDirectory_Model_Source_Regency
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Model_Source_Regency
{
	public function toOptionArray($type = 0, $filterByProvinceCode = '')
	{
		$options = array();
		$collection = Mage::getModel('ksdirectory/regency')->getCollection();

		if($type == 0){
			$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('regency_name')->order('regency_name', 'ASC');
			if($collection->getData()){
				foreach ($collection->getData() as $key => $value) {
					$options[$value['regency_name']] = $value['regency_name'];
				}
			}
		}
		elseif($type == 1){
			$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('regency_code')->columns('regency_name')->order('regency_name', 'ASC');
			if($collection->getData()){
				$options[''] = '-- Please Select --';
				foreach ($collection->getData() as $key => $value) {
					$options[$value['regency_code']] = $value['regency_name'];
				}
			}
		}
		elseif($type == 2)
		{
			if($filterByProvinceCode != ''){
				$collection->addFieldToFilter('province_code', $filterByProvinceCode);

				$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('regency_code')->columns('regency_name')->order('regency_name', 'ASC');

				if($collection->getData()){
					$options[''] = '-- Please Select --';
					foreach ($collection->getData() as $value) {
						$options[$value['regency_code']] = $value['regency_name'];
					}
				}
			}

		}
		elseif($type == 3)
		{
			$collection = Mage::getModel('ksdirectory/grid')->getCollection();
			if($filterByProvinceCode != '') $collection->addFieldToFilter('province_code', $filterByProvinceCode);
			$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('province_name')->columns('regency_name')->columns('regency_code');
			$collection->getSelect()->group('regency_code')->order('province_name', 'ASC')->order('regency_name', 'ASC');

			$countries = array();
			if($collection->getData()){
				foreach ($collection->getData() as $country) {
	                $countries['Indonesia'][$country['province_name']][$country['regency_name']] = $country['regency_code'];
	            }

	            foreach ($countries as $country => $_provinces){
	            	$label = '';
	            	foreach ($_provinces as $_province => $_regions) {
	            		$regions = array();
	            		foreach ($_regions as $key => $val) {
	            			$regions[] = array('label' => $key, 'value' => $val);
	            		}
	            		$label = $country.' / '.$_province;
	            		$options[] = array('label' => $label, 'value' => $regions);
	            	}
	            }
	        } 
		}
		
		return $options;
	}
}
