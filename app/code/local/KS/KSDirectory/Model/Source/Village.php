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
 * Source Model KS_KSDirectory_Model_Source_Village
 *
 * @author      Putu Ardika <pardika@kemanaservices.com>
 */

class KS_KSDirectory_Model_Source_Village
{
	public function toOptionArray($type = 0, $filterBySubdistrictCode = '')
	{
		$options = array();
		$collection = Mage::getModel('ksdirectory/village')->getCollection();

		if($type == 0){
			$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('village_name')->order('village_name', 'ASC');
			if($collection->getData()){
				foreach ($collection->getData() as $key => $value) {
					$options[$value['village_name']] = $value['village_name'];
				}
			}
		}
		elseif($type == 1){
			$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('village_code')->columns('village_name')->order('village_name', 'ASC');
			if($collection->getData()){
				$options[''] = '-- Please Select --';
				foreach ($collection->getData() as $key => $value) {
					$options[$value['village_code']] = $value['village_name'];
				}
			}
		}
		elseif($type == 2)
		{	
			if($filterBySubdistrictCode != ''){
				$collection->addFieldToFilter('subdistrict_code', $filterBySubdistrictCode);

				$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('village_code')->columns('village_name')->order('village_name', 'ASC');

				if($collection->getData()){
					$options[''] = '-- Please Select --';
					foreach ($collection->getData() as $value) {
						$options[$value['village_code']] = $value['village_name'];
					}
				}
			}

		}
		
		return $options;
	}
}
