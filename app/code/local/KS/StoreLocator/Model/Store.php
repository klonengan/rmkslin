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
 * @package     KS_StoreLocator
 * @copyright   Copyright (c) 2014 Kemana Services (http://www.kemanaservices.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * KS_StoreLocator_Model_Store
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_StoreLocator_Model_Store extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    public function _construct()
    {
        $this->_init('storelocator/store');
    }

    protected function _getAvailableRegions()
    {
    	$collection = $this->getCollection();
        $collection->getSelect()->join(
            array('d'=>'ks_directory_province'),
            'main_table.region_id = d.province_code',
            array('d.province_name')
        );
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('d.province_name')->group('d.province_name');
        $collection->setOrder('province_name', 'ASC');
        //$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('id')->group('id');
    	//$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('province_name')->group('province_name');
        //$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('region_id')->group('region_id');
        return $collection;
    }

    public function getAvailableRegions()
    {
    	$collection = $this->_getAvailableRegions();

        $regions = array();
    	foreach ($collection as $region) {
    		$regions[] = '"'.$region->getProvinceName().'"';
    	}
    	$output = '['.implode(',', $regions).']';

        return $output;

        //return $collection;
    }

    public function getAvailableRegionsForList()
    {
    	return $this->_getAvailableRegions();
    }


    /* city  */
    protected function _getAvailableCities()
    {
    	$collection = $this->getCollection();
        $collection->getSelect()->join(
            array('p'=>'ks_directory_province'),
            'main_table.region_id = p.province_code',
            array('p.province_name')
        );
        $collection->getSelect()->join(
            array('c'=>'ks_directory_regency'),
            'main_table.city = c.regency_code',
            array('c.regency_name')
        );
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('main_table.region_id')->columns('main_table.city')->columns('c.regency_name')->group('main_table.city');
    	$collection->setOrder('main_table.region_id', 'ASC');
    	$collection->setOrder('main_table.city', 'ASC');

    	return $collection;
    }

    public function getAvailableCities()
    {
    	$collection = $this->_getAvailableCities();
    	$cities = array();
    	foreach ($collection as $city) {
    		$cities[] = '"'.$city->getRegencyName().'"';
    	}
    	$output = '['.implode(',', $cities).']';
    	return $output;
    }

    public function getAvailableCitiesForRegion()
    {
    	$collection = $this->_getAvailableCities();
    	$regions = array();
    	foreach ($collection as $value) {
    		$regions[$value->getRegionId()][] = '"'.$value->getRegencyName().'"';
    	}
    	$cities = array();
    	foreach ($regions as $value) {
    		$cities[] = '['.implode(',', $value).']';
    	}
    	$output = '['.implode(',', $cities).']';
        return $output;
    }

    public function getAvailableStores()
    {
    	$jsScript = '';
    	$collection = $this->_getAvailableCities();
    	$idxCity = 0;
    	foreach ($collection as $city) {
    		$stores = array();
    		$storeCollection = $this->getCollection();
    		$storeCollection->setOrder('id', 'ASC');

            $storeCollection->getSelect()->where('region_id = "'.$city->getRegionId().'" AND city = "'.$city->getCity().'"');

    		$idxStore = 0;
    		foreach ($storeCollection as $value) {
    			/*
                $storeOperations = '';
    			if(strpos($value->getStoreOperations(), '|') > 0){
    				$storeOperations = str_replace('|', '<br>', $value->getStoreOperations());
    			}
    			$stores[$city->getCity()][$idxStore][] = '"'.$value->getStoreName().'"';
    			$stores[$city->getCity()][$idxStore][] = $value->getGoogleLatitude();
    			$stores[$city->getCity()][$idxStore][] = $value->getGoogleLongitude();
    			$stores[$city->getCity()][$idxStore][] = '"'.$value->getStoreAddress1().'"';
    			$stores[$city->getCity()][$idxStore][] = '"'.$value->getStoreAddress2().'"';
    			$stores[$city->getCity()][$idxStore][] = '"'.$value->getStorePhone().'"';
    			$stores[$city->getCity()][$idxStore][] = '"'.$value->getStoreFax().'"';
    			$stores[$city->getCity()][$idxStore][] = '"'.$storeOperations.'"';
			    $stores[$city->getCity()][$idxStore][] = '"'.$value->getIsHotspot().'"';
    			*/
                $stores[$city->getCity()][$idxStore][] = '"'.$value->getName().'"';
                $stores[$city->getCity()][$idxStore][] = $value->getLat();
                $stores[$city->getCity()][$idxStore][] = $value->getLong();
                $stores[$city->getCity()][$idxStore][] = '"'.$value->getAddress().'"';
                $stores[$city->getCity()][$idxStore][] = '"'.Mage::getStoreConfig('general/store_information/hours').'"';
    			$idxStore++;
    		}
            Mage::log((string)$storeCollection->getSelect(),null,'test.log',true);
    		$store = array();
    		foreach ($stores as $_store) {
    			foreach ($_store as $value) {
    				$store[] = '['.implode(',', $value).']';
    			}
	    	}
	    	$jsScript .= ' storeData['.$idxCity.'] = ['.implode(',', $store).'];'.PHP_EOL;
	    	$idxCity++;


    	}

    	return $jsScript;
    }

}
