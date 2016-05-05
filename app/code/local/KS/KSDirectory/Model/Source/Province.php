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
 * Source Model KS_KSDirectory_Model_Source_Province
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Model_Source_Province
{

	public function toOptionArray($type = 0, $filterByCountryCode = '')
	{
		$options = array();
		$collection = Mage::getModel('ksdirectory/province')->getCollection();

		if($type == 0)
		{
			$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('province_name')->order('province_name', 'ASC');

			if($collection->getData()){
				foreach ($collection->getData() as $value) {
					$options[$value['province_name']] = $value['province_name'];
				}
			}
		}
		elseif($type == 1)
		{
			$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('province_code')->columns('province_name')->order('province_name', 'ASC');

			if($collection->getData()){
				$options[''] = '-- Please Select --';
				foreach ($collection->getData() as $value) {
					$options[$value['province_code']] = $value['province_name'];
				}
			}
		}
		elseif($type == 2)
		{
			if($filterByCountryCode != ''){
				$collection->addFieldToFilter('country_code', $filterByCountryCode);

				$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('province_code')->columns('province_name')->order('province_name', 'ASC');
				if($collection->getData()){
					$options[''] = '-- Please Select --';
					foreach ($collection->getData() as $value) {
						$options[$value['province_code']] = $value['province_name'];
					}
				}
			}
		}
		elseif($type == 3)
		{
			$collection = Mage::getModel('ksdirectory/grid')->getCollection();
			if($filterByCountryCode != '') $collection->addFieldToFilter('country_code', $filterByCountryCode);
			$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('province_code')->columns('province_name');
			$collection->getSelect()->group('province_code')->order('province_name', 'ASC');

			$provinces = array();
			if($collection->getData()){
				foreach ($collection->getData() as $province) {
					$provinces[] = array('label' => $province['province_name'], 'value' => $province['province_code']);
	            }
	            $options[] = array('label' => 'Indonesia', 'value' => $provinces);
	        }    
		}
		
		return $options;
	}
}
