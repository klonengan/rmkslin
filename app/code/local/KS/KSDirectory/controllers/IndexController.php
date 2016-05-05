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
 * Index controller
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function reloadprovinceAction(){
        $country = $this->getRequest()->getPost('c');
        $def = $this->getRequest()->getPost('d');
        $key = $this->getRequest()->getPost('k');
        $response['status'] = 'error';
        $response['html'] = '<option value="">---</option>';
        if(Mage::getSingleton('core/session')->getFormKey() == $key && $country){
            $collection = Mage::getModel('ksdirectory/province')->getCollection();
            $collection->addFieldToFilter('country_code', $country);
            $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('province_code')->columns('province_name')->order('province_name', 'ASC');
            if($collection->getData()){
                $province = '<option value="">'.$this->__('Select Province').'</option>';
                foreach ($collection->getData() as $key => $value) {
                    if($def != '' && $def == $value['province_code']){
                        $selected = 'selected="selected"';
                    }
                    else{
                         $selected = '';
                    }
                    $province .= '<option '.$selected.' value="'.$value['province_name'].'" data-code="'.$value['province_code'].'">'.$value['province_name'].'</option>';
                }
                $response['status'] = 'success';
                $response['html'] = $province;
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    public function reloadcityAction(){
        $province = $this->getRequest()->getParam('p');
        $def = $this->getRequest()->getParam('d');
        $key = $this->getRequest()->getPost('k');
        $response['status'] = 'error';
        $response['html'] = '<option value="">---</option>';
        if($province){
            $collection = Mage::getModel('ksdirectory/regency')->getCollection();
            $collection->addFieldToFilter('province_code', $province);
            $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('regency_code')->columns('regency_name')->order('regency_name', 'ASC');
            if($collection->getData()){
                $regency = '<option value="">'.$this->__('Select City or District').'</option>';
                foreach ($collection->getData() as $key => $value) {
                    if($def != '' && $def == $value['regency_code']){
                        $selected = 'selected="selected"';
                    }
                    else{
                         $selected = '';
                    }
                    $regency .= '<option '.$selected.' value="'.$value['regency_code'].'" data-code="'.$value['regency_name'].'">'.$value['regency_name'].'</option>';
                }
                $response['status'] = 'success';
                $response['html'] = $regency;
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

     public function reloadsubdistrictAction(){
        $regency = $this->getRequest()->getParam('r');
        $def = $this->getRequest()->getParam('d');
        $key = $this->getRequest()->getPost('k');
        $response['status'] = 'error';
        $response['html'] = '<option value="">---</option>';
        if(Mage::getSingleton('core/session')->getFormKey() == $key && $regency){
            $collection = Mage::getModel('ksdirectory/subdistrict')->getCollection();
            $collection->addFieldToFilter('regency_code', $regency);
            $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('subdistrict_code')->columns('subdistrict_name')->order('subdistrict_name', 'ASC');
            if($collection->getData()){
                $subdistrict = '<option value="">'.$this->__('Select Sub-District').'</option>';
                foreach ($collection->getData() as $key => $value) {
                    if($def != '' && $def == $value['subdistrict_code']){
                        $selected = 'selected="selected"';
                    }
                    else{
                         $selected = '';
                    }
                    $subdistrict .= '<option '.$selected.' value="'.$value['subdistrict_name'].'" data-code="'.$value['subdistrict_code'].'">'.$value['subdistrict_name'].'</option>';
                }
                $response['status'] = 'success';
                $response['html'] = $subdistrict;
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    public function suggestionzipcodeAction(){
        $subdistrict = $this->getRequest()->getParam('s');
        $key = $this->getRequest()->getPost('k');
        $response['status'] = 'error';
        $response['html'] = '';
        $response['hint'] = '';
        $response['count'] = 0;
        if(Mage::getSingleton('core/session')->getFormKey() == $key && $subdistrict){
            $collection = Mage::getModel('ksdirectory/village')->getCollection();
            $collection->addFieldToFilter('subdistrict_code', $subdistrict);
            $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('village_name')->columns('postcode')->order('postcode', 'ASC');//->distinct(true);
            $count = $collection->getSize();
            if($collection->getData()){
                $zipcodes = '<ul>';
                $hint = '';
                foreach ($collection->getData() as $key => $value) {
                    $zipcodes .= '<li><a href="javascript:void(0)" class="linkSuggestPostCode" data-val="'.$value['postcode'].'"><strong>'.$value['village_name'].'</strong>, '.$value['postcode'].'</a></li>';
                    $hint = $hint == '' ? substr($value['postcode'], 0, 2).'xxx' : $hint;
                }
                $zipcodes .= '</ul>';
            }
            $response['status'] = 'success';
            $response['html'] = $zipcodes;
            $response['hint'] = $hint;
            $response['count'] = $count;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

 	public function branchAction(){
		setcookie(KS_Price_Helper_Data::COOKIE_NAME, 1, time()+ KS_Price_Helper_Data::COOKIE_TIME_LENGTH, "/");
	}
   
}