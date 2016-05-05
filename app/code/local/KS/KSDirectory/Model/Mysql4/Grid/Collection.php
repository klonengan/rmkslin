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
 * KS_KSDirectory_Model_Mysql4_Grid_Collection
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Model_Mysql4_Grid_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('ksdirectory/grid');
    }

    public function loadById($id){
        $this->addFieldToFilter('id', $id);
        return $this;
    }

    public function loadByProvinceCode($provinceCode){
        $this->addFieldToFilter('province_code', $provinceCode);
        return $this;
    }

    public function loadByRegencyCode($regencyCode){
        $this->addFieldToFilter('regency_code', $regencyCode);
        return $this;
    }

    public function loadBySubdistrictCode($subdistrictCode){
        $this->addFieldToFilter('subdistrict_code', $subdistrictCode);
        return $this;
    }

    public function loadByVillageCode($villageCode){
        $this->addFieldToFilter('village_code', $villageCode);
        return $this;
    }

    public function addCustomFilter($field, $operator, $value){
        $this->addFieldToFilter($field, array($operator => $value));
        return $this;
    }

    public function getFirstItem(){
        $this->setOrder('id', 'ASC')->count();
        $this->getSelect()->limit(1);
        return $this;
    }

    public function getLastItem(){
        $this->setOrder('id', 'DESC')->count();
        $this->getSelect()->limit(1);
        return $this;
    }

    public function getId()
    {
        $collection = $this->getData();
        return @$collection[0]['id'];
    }

    public function getCountryCode()
    {
    	$collection = $this->getData();
    	return @$collection[0]['country_code'];
    }

    public function getCountryName()
    {
        $collection = $this->getData();
        return @$collection[0]['country_name'];
    }

    public function getProvinceCode()
    {
        $collection = $this->getData();
        return @$collection[0]['province_code'];
    }

    public function getProvinceName()
    {
        $collection = $this->getData();
        return @$collection[0]['province_name'];
    }

    public function getRegencyCode()
    {
        $collection = $this->getData();
        return @$collection[0]['regency_code'];
    }

    public function getRegencyName()
    {
        $collection = $this->getData();
        return @$collection[0]['regency_name'];
    }

    public function getSubdistrictCode()
    {
        $collection = $this->getData();
        return @$collection[0]['subdistrict_code'];
    }

    public function getSubdistrictName()
    {
        $collection = $this->getData();
        return @$collection[0]['subdistrict_name'];
    }

    public function getVillageCode()
    {
        $collection = $this->getData();
        return @$collection[0]['village_code'];
    }

    public function getVillageName()
    {
        $collection = $this->getData();
        return @$collection[0]['village_name'];
    }

    public function getPostcode()
    {
        $collection = $this->getData();
        return @$collection[0]['postcode'];
    }
    
}