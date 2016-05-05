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
 * KS_KSDirectory_Model_Mysql4_Province_Collection
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Model_Mysql4_Province_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('ksdirectory/province');
    }

    public function loadById($id){
        $this->addFieldToFilter('id', $id);
        return $this;
    }

    public function loadByCode($provinceCode){
        $this->addFieldToFilter('province_code', $provinceCode);
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

    public function getActive()
    {
    	$collection = $this->getData();
    	return @$collection[0]['active'];
    }

    public function toOptionArray()
    {
        $collection = $this->getData();
        $data[0] = array('value'=>'0','label'=>'Select Province');
        foreach($collection as $propinsi => $value){
            $data[] = array(
                'value'=>$value['province_code'],
                'label'=>$value['province_name']
            );
        }
        return $data;
    }

    public function toOptionArrayCustom()
    {
        $collection = $this->getData();
        $data[0] = array('value'=>'0','label'=>'Select Province');
        foreach($collection as $propinsi => $value){
            $data[] = array(
                'value'=>$value['province_name'],
                'label'=>$value['province_name']
            );
        }
        return $data;
    }

}