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

class KS_KSDirectory_Adminhtml_Ksdirectory_DirectoryController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction()
    {
		$this->loadLayout()
			->_setActiveMenu('ksall')
			->_addBreadcrumb(Mage::helper('ksdirectory')->__('Directory'), Mage::helper('ksdirectory')->__('Directory'));
        return $this;
    }

    public function indexAction()
    {
        if($this->_isAllowed()){
            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('ksdirectory/adminhtml_directory'));
            $this->renderLayout();
        }
    }

    public function newprovinceAction()
    {
        if($this->_isAllowed()){

            $_model = Mage::getModel('ksdirectory/province');
            Mage::register('ksdirectory_province_data', $_model);

            $this->_initAction();
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit'))
                    ->_addLeft($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit_tabs'));

            $this->renderLayout();
        }
    }

    public function editprovinceAction()
    {
        if($this->_isAllowed()){
            $gid = $this->getRequest()->getParam('gid');
            $provinceCode = Mage::getModel('ksdirectory/grid')->getCollection()->loadById($gid)->getFirstItem()->getProvinceCode();
            if($provinceCode){
                $_model = Mage::getModel('ksdirectory/province');
                $id = $_model->getCollection()->loadByCode($provinceCode)->getFirstItem()->getId();
                if($id){
                    Mage::register('ksdirectory_province_data', $_model->load($id));

                    $this->_initAction();
                    $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
                    $this->_addContent($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit'))
                            ->_addLeft($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit_tabs'));

                    $this->renderLayout();
                }
                else{
                    $this->_redirect('*/*/index');
                }
            }
            else{
                $this->_redirect('*/*/index');
            }
        }
    }

    public function saveprovinceAction()
    {
        if($this->_isAllowed()){
            if($arrData = $this->getRequest()->getPost())
            {
                $_model = Mage::getModel('ksdirectory/province');
                $gid = $this->getRequest()->getParam('gid');
                try
                {
                    if($gid)
                    {
                        // Update Data Province in GRID table
                        $_grid = Mage::getModel('ksdirectory/grid')->getCollection()->loadById($gid)->getFirstItem();
                        $oldCountryCode = $_grid->getCountryCode();
                        $updateMaster = false;
                        if($oldCountryCode != $arrData['country_code']){
                            $oldProvinceCode = $_grid->getProvinceCode();

                            $lastCode = (int) substr(Mage::getModel('ksdirectory/province')->getCollection()->addFieldToFilter('country_code', $arrData['country_code'])->getLastItem()->getProvinceCode(), -3);
                            $newProvinceCode = $arrData['country_code'].str_pad($lastCode + 1, 3, "0", STR_PAD_LEFT);

                            $arrData['province_code'] = '';
                            $updateGrid = Mage::getModel('ksdirectory/mysql4_grid')->updateProvince($oldProvinceCode, $newProvinceCode, $arrData['province_name']);
                            if($updateGrid){
                                $updateRegency = Mage::getModel('ksdirectory/mysql4_regency')->updateProvinceCode($oldProvinceCode, $newProvinceCode);
                            }
                            if($updateRegency){
                                $updateSubdistrict = Mage::getModel('ksdirectory/mysql4_subdistrict')->updateProvinceCode($oldProvinceCode, $newProvinceCode);
                            }
                            if($updateSubdistrict){
                                $updateVillage = Mage::getModel('ksdirectory/mysql4_village')->updateProvinceCode($oldProvinceCode, $newProvinceCode);
                            }
                            if($updateVillage){
                                $arrData['province_code'] = $newProvinceCode;
                                $updateMaster = true;
                            }
                        }
                        else
                        {
                            $oldProvinceCode = $_grid->getProvinceCode();
                            $updateGrid = Mage::getModel('ksdirectory/mysql4_grid')->updateProvinceName($oldProvinceCode, $arrData['province_name']);
                            if($updateGrid) $updateMaster = true;
                        }

                        if($updateMaster){
                            $_model->setData($arrData);
                            $_model->save();
                            $countryName = Mage::app()->getLocale()->getCountryTranslation($arrData['country_code']);
                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ksdirectory')->__('Province code %s in %s has been updated', $arrData['province_code'], $countryName));
                        }
                        else{
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ksdirectory')->__('There was an error when updating this province.'));
                        }
                        
                    }
                    else{ 
                        $lastCode = (int) substr(Mage::getModel('ksdirectory/province')->getCollection()->addFieldToFilter('country_code', $arrData['country_code'])->getLastItem()->getProvinceCode(), -3);
                        $provinceCode = $arrData['country_code'].str_pad($lastCode + 1, 3, "0", STR_PAD_LEFT);
                        $arrData['province_code'] = $provinceCode;
                        $_model->setData($arrData);
                        $_model->save();
                        $countryName = Mage::app()->getLocale()->getCountryTranslation($arrData['country_code']);
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ksdirectory')->__('Province %s has been added to %s', $arrData['province_name'], $countryName));
                    }
                    
                    $this->_redirect('*/*/');
                    return;
                }
                catch (Exception $e)
                {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($arrData);
                    $this->_redirect('*/*/editprovince', array('gid' => $gid));
                    return;
                }
            }
        }
    }

    public function newregencyAction()
    {
        if($this->_isAllowed()){

            $_model = Mage::getModel('ksdirectory/regency');
            Mage::register('ksdirectory_regency_data', $_model);

            $this->_initAction();
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit'))
                    ->_addLeft($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit_tabs'));

            $this->renderLayout();
        }
    }

    public function editregencyAction()
    {
        if($this->_isAllowed()){
            $gid = $this->getRequest()->getParam('gid');
            $regencyCode = Mage::getModel('ksdirectory/grid')->getCollection()->loadById($gid)->getFirstItem()->getRegencyCode();
            if($regencyCode){
                $_model = Mage::getModel('ksdirectory/regency');
                $id = $_model->getCollection()->loadByCode($regencyCode)->getFirstItem()->getId();
                if($id){
                    Mage::register('ksdirectory_regency_data', $_model->load($id));

                    $this->_initAction();
                    $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
                    $this->_addContent($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit'))
                            ->_addLeft($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit_tabs'));

                    $this->renderLayout();
                }
                else{
                    $this->_redirect('*/*/index');
                }
            }
            else{
                $this->_redirect('*/*/index');
            }
        }
    }

    public function saveregencyAction()
    {
        if($this->_isAllowed()){
            if($arrData = $this->getRequest()->getPost())
            {
                $_model = Mage::getModel('ksdirectory/regency');
                $gid = $this->getRequest()->getParam('gid');
                try
                {
                    if($gid)
                    {
                        // Update Data Province in GRID table
                        $_grid = Mage::getModel('ksdirectory/grid')->getCollection()->loadById($gid)->getFirstItem();
                        $oldProvinceCode = $_grid->getProvinceCode();
                        $updateMaster = false;
                        if($oldProvinceCode != $arrData['province_code']){
                            $oldRegencyCode = $_grid->getRegencyCode();
                            
                            $lastRegencyCode = (int) substr(Mage::getModel('ksdirectory/regency')->getCollection()->addFieldToFilter('province_code', $arrData['province_code'])->getLastItem()->getRegencyCode(), -3);
                            $newRegencyCode = $arrData['province_code'].str_pad($lastRegencyCode + 1, 3, "0", STR_PAD_LEFT);

                            $arrData['regency_code'] = '';
                            $updateGrid = Mage::getModel('ksdirectory/mysql4_grid')->updateRegency($oldRegencyCode, $newRegencyCode, $arrData['regency_name']);
                            if($updateGrid){
                                $updateSubdistrict = Mage::getModel('ksdirectory/mysql4_subdistrict')->updateRegencyCode($oldRegencyCode, $newRegencyCode);
                            }
                            if($updateSubdistrict){
                                $updateVillage = Mage::getModel('ksdirectory/mysql4_village')->updateRegencyCode($oldRegencyCode, $newRegencyCode);
                            }
                            if($updateVillage){
                                $arrData['regency_code'] = $newRegencyCode;
                                $updateMaster = true;
                            }
                        }
                        else
                        {
                            $oldRegencyCode = $_grid->getRegencyCode();
                            $updateGrid = Mage::getModel('ksdirectory/mysql4_grid')->updateRegencyName($oldRegencyCode, $arrData['regency_name']);
                            if($updateGrid) $updateMaster = true;
                        }

                        if($updateMaster){
                            $_model->setData($arrData);
                            $_model->save();
                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ksdirectory')->__('Regency/city code %s has been updated', $arrData['regency_code']));
                        }
                        else{
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ksdirectory')->__('There was an error when updating this regency/city.'));
                        }
                        
                    }
                    else{
                        $lastCode = (int) substr(Mage::getModel('ksdirectory/regency')->getCollection()->addFieldToFilter('province_code', $arrData['province_code'])->getLastItem()->getRegencyCode(), -3);
                        $regencyCode = $arrData['province_code'].str_pad($lastCode + 1, 3, "0", STR_PAD_LEFT);
                        $arrData['regency_code'] = $regencyCode;
                        $_model->setData($arrData);
                        $_model->save();
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ksdirectory')->__('Regency/city %s has been added', $arrData['regency_name']));
                    }
                    
                    $this->_redirect('*/*/');
                    return;
                }
                catch (Exception $e)
                {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($arrData);
                    $this->_redirect('*/*/editregency', array('gid' => $gid));
                    return;
                }
            }
        }
    }

    public function newsubdistrictAction()
    {
        if($this->_isAllowed()){

            $_model = Mage::getModel('ksdirectory/subdistrict');
            Mage::register('ksdirectory_subdistrict_data', $_model);

            $this->_initAction();
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit'))
                    ->_addLeft($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit_tabs'));

            $this->renderLayout();
        }
    }

    public function editsubdistrictAction()
    {
        if($this->_isAllowed()){
            $gid = $this->getRequest()->getParam('gid');
            $subdistrictCode = Mage::getModel('ksdirectory/grid')->getCollection()->loadById($gid)->getFirstItem()->getSubdistrictCode();
            if($subdistrictCode){
                $_model = Mage::getModel('ksdirectory/subdistrict');
                $id = $_model->getCollection()->loadByCode($subdistrictCode)->getFirstItem()->getId();
                if($id){
                    Mage::register('ksdirectory_subdistrict_data', $_model->load($id));

                    $this->_initAction();
                    $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
                    $this->_addContent($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit'))
                            ->_addLeft($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit_tabs'));

                    $this->renderLayout();
                }
                else{
                    $this->_redirect('*/*/index');
                }
            }
            else{
                $this->_redirect('*/*/index');
            }
        }
    }

    public function savesubdistrictAction()
    {
        if($this->_isAllowed()){
            if($arrData = $this->getRequest()->getPost())
            {
                $_model = Mage::getModel('ksdirectory/subdistrict');
                $gid = $this->getRequest()->getParam('gid');
                try
                {
                    if($gid)
                    {
                        // Update Data Province in GRID table
                        $_grid = Mage::getModel('ksdirectory/grid')->getCollection()->loadById($gid)->getFirstItem();
                        $oldRegencyCode = $_grid->getRegencyCode();
                        $updateMaster = false;
                        if($oldRegencyCode != $arrData['regency_code']){
                            $oldSubdistrictCode = $_grid->getSubdistrictCode();
                            
                            $lastCode = (int) substr(Mage::getModel('ksdirectory/subdistrict')->getCollection()->addFieldToFilter('regency_code', $arrData['regency_code'])->getLastItem()->getSubdistrictCode(), -3);
                            $newSubdistrictCode = $arrData['regency_code'].str_pad($lastCode + 1, 3, "0", STR_PAD_LEFT);

                            $arrData['subdistrict_code'] = '';
                            $updateGrid = Mage::getModel('ksdirectory/mysql4_grid')->updateSubdistrict($oldSubdistrictCode, $newSubdistrictCode, $arrData['subdistrict_name']);
                            
                            if($updateGrid){
                                $updateVillage = Mage::getModel('ksdirectory/mysql4_village')->updateSubdistrictCode($oldSubdistrictCode, $newSubdistrictCode);
                            }
                            if($updateVillage){
                                $arrData['subdistrict_code'] = $newSubdistrictCode;
                                $updateMaster = true;
                            }
                        }
                        else
                        {
                            $oldSubdistrictCode = $_grid->getSubdistrictCode();
                            $updateGrid = Mage::getModel('ksdirectory/mysql4_grid')->updateSubdistrictName($oldSubdistrictCode, $arrData['subdistrict_name']);
                            if($updateGrid) $updateMaster = true;
                        }

                        if($updateMaster){
                            $_model->setData($arrData);
                            $_model->save();
                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ksdirectory')->__('Subdistrict code %s has been updated', $arrData['subdistrict_code']));
                        }
                        else{
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ksdirectory')->__('There was an error when updating this subdistrict.'));
                        }
                        
                    }
                    else{
                        $lastCode = (int) substr(Mage::getModel('ksdirectory/subdistrict')->getCollection()->addFieldToFilter('regency_code', $arrData['regency_code'])->getLastItem()->getSubdistrictCode(), -3);
                        $subdistrictCode = $arrData['regency_code'].str_pad($lastCode + 1, 3, "0", STR_PAD_LEFT);
                        $arrData['subdistrict_code'] = $subdistrictCode;
                        $_model->setData($arrData);
                        $_model->save();
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ksdirectory')->__('Subdistrict %s has been added', $arrData['subdistrict_name']));
                    }
                    
                    $this->_redirect('*/*/');
                    return;
                }
                catch (Exception $e)
                {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($arrData);
                    $this->_redirect('*/*/editsubdistrict', array('gid' => $gid));
                    return;
                }
            }
        }
    }

    public function newvillageAction()
    {
        if($this->_isAllowed()){

            $_model = Mage::getModel('ksdirectory/village');
            Mage::register('ksdirectory_village_data', $_model);

            $this->_initAction();
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit'))
                    ->_addLeft($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit_tabs'));

            $this->renderLayout();
        }
    }

    public function editvillageAction()
    {
        if($this->_isAllowed()){
            $gid = $this->getRequest()->getParam('gid');
            $villageCode = Mage::getModel('ksdirectory/grid')->getCollection()->loadById($gid)->getFirstItem()->getVillageCode();
            if($villageCode){
                $_model = Mage::getModel('ksdirectory/village');
                $id = $_model->getCollection()->loadByCode($villageCode)->getFirstItem()->getId();
                if($id){
                    Mage::register('ksdirectory_village_data', $_model->load($id));

                    $this->_initAction();
                    $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
                    $this->_addContent($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit'))
                            ->_addLeft($this->getLayout()->createBlock('ksdirectory/adminhtml_directory_edit_tabs'));

                    $this->renderLayout();
                }
                else{
                    $this->_redirect('*/*/index');
                }
            }
            else{
                $this->_redirect('*/*/index');
            }
        }
    }

    public function savevillageAction()
    {
        if($this->_isAllowed()){
            if($arrData = $this->getRequest()->getPost())
            {
                $_model = Mage::getModel('ksdirectory/village');
                $gid = $this->getRequest()->getParam('gid');
                try
                {
                    if($gid)
                    {
                        // Update Data Province in GRID table
                        $_grid = Mage::getModel('ksdirectory/grid')->getCollection()->loadById($gid)->getFirstItem();
                        $oldSubdistrictCode = $_grid->getSubdistrictCode();
                        $updateMaster = false;
                        if($oldSubdistrictCode != $arrData['subdistrict_code']){
                            $oldVillageCode = $_grid->getVillageCode();
                            
                            $lastCode = (int) substr(Mage::getModel('ksdirectory/village')->getCollection()->addFieldToFilter('subdistrict_code', $arrData['subdistrict_code'])->getLastItem()->getVillageCode(), -3);
                            $newVillageCode = $arrData['subdistrict_code'].str_pad($lastCode + 1, 3, "0", STR_PAD_LEFT);

                            $arrData['village_code'] = '';
                            $updateGrid = Mage::getModel('ksdirectory/mysql4_grid')->updateVillage($oldVillageCode, $newVillageCode, $arrData['village_name'], $arrData['postcode']);
                            if($updateGrid){
                                $arrData['village_code'] = $newVillageCode;
                                $updateMaster = true;
                            }
                        }
                        else
                        {
                            $oldVillageCode = $_grid->getVillageCode();
                            $updateGrid = Mage::getModel('ksdirectory/mysql4_grid')->updateVillageName($oldVillageCode, $arrData['village_name'], $arrData['postcode']);
                            if($updateGrid) $updateMaster = true;
                        }

                        if($updateMaster){
                            $_model->setData($arrData);
                            $_model->save();
                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ksdirectory')->__('Village code %s has been updated', $arrData['village_code']));
                        }
                        else{
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ksdirectory')->__('There was an error when updating this village.'));
                        }
                        
                    }
                    else{
                        $lastCode = (int) substr(Mage::getModel('ksdirectory/village')->getCollection()->addFieldToFilter('subdistrict_code', $arrData['subdistrict_code'])->getLastItem()->getVillageCode(), -3);
                        $villageCode = $arrData['subdistrict_code'].str_pad($lastCode + 1, 3, "0", STR_PAD_LEFT);
                        $arrData['village_code'] = $villageCode;
                        $_model->setData($arrData);
                        $_model->save();

                        $provinceName = Mage::getModel('ksdirectory/province')->getCollection()->loadByCode($arrData['province_code'])->getFirstItem()->getProvinceName();
                        $regencyName = Mage::getModel('ksdirectory/regency')->getCollection()->loadByCode($arrData['regency_code'])->getFirstItem()->getRegencyName();
                        $subdistrictName = Mage::getModel('ksdirectory/subdistrict')->getCollection()->loadByCode($arrData['subdistrict_code'])->getFirstItem()->getSubdistrictName();

                        $_grid = Mage::getModel('ksdirectory/grid');
                        $_grid->setCountryCode($arrData['country_code']);
                        $_grid->setProvinceCode($arrData['province_code']);
                        $_grid->setProvinceName($provinceName);
                        $_grid->setRegencyCode($arrData['regency_code']);
                        $_grid->setRegencyName($regencyName);
                        $_grid->setSubdistrictCode($arrData['subdistrict_code']);
                        $_grid->setSubdistrictName($subdistrictName);
                        $_grid->setVillageCode($arrData['village_code']);
                        $_grid->setVillageName($arrData['village_name']);
                        $_grid->setPostcode($arrData['postcode']);
                        $_grid->save();

                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ksdirectory')->__('Village %s has been added', $arrData['village_name']));
                    }
                    
                    $this->_redirect('*/*/');
                    return;
                }
                catch (Exception $e)
                {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($arrData);
                    $this->_redirect('*/*/editvillage', array('gid' => $gid));
                    return;
                }
            }
        }
    }

    public function reloadprovinceAction(){
        $country = $this->getRequest()->getParam('country');
        $mode = $this->getRequest()->getParam('mo') ? $this->getRequest()->getParam('mo') : 0;
        $def = $this->getRequest()->getParam('dv') ? $this->getRequest()->getParam('dv') : '';
        if($country){
            $province = '';
            $collection = Mage::getModel('ksdirectory/province')->getCollection();
            $collection->addFieldToFilter('country_code', $country);
            $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('province_code')->columns('province_name')->order('province_name', 'ASC');
            if($collection->getData()){
                if($mode == 0){
                    $province = '<option value="">'.Mage::helper('ksdirectory')->__('-- Please Select --').'</option>';
                }elseif($mode == 1){
                    $province = '<option value="" data-code="">'.Mage::helper('ksdirectory')->__('-- Please Select --').'</option>';
                }
                foreach ($collection->getData() as $key => $value) {
                    $selected = $def && $def == $value['province_code'] ? ' selected="selected" ' : '';
                    if($mode == 0){
                        $province .= '<option value="'.$value['province_code'].'" '.$selected.'>'.$value['province_name'].'</option>';
                    }elseif($mode == 1){
                        $province .= '<option value="'.$value['province_name'].'" data-code="'.$value['province_code'].'" '.$selected.'>'.$value['province_name'].'</option>';
                    }
                }
            }
            echo $province;
        }
    }

    public function reloadregencyAction(){
        $province = $this->getRequest()->getParam('province');
        $mode = $this->getRequest()->getParam('mo') ? $this->getRequest()->getParam('mo') : 0;
        $def = $this->getRequest()->getParam('dv') ? $this->getRequest()->getParam('dv') : '';
        if($province){
            $regency = '';
            $collection = Mage::getModel('ksdirectory/regency')->getCollection();
            $collection->addFieldToFilter('province_code', $province);
            $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('regency_code')->columns('regency_name')->order('regency_name', 'ASC');
            if($collection->getData()){
                if($mode == 0){
                    $regency = '<option value="">'.Mage::helper('ksdirectory')->__('-- Please Select --').'</option>';
                }elseif($mode == 1){
                    $regency = '<option value="" data-code="">'.Mage::helper('ksdirectory')->__('-- Please Select --').'</option>';
                }
                foreach ($collection->getData() as $key => $value) {
                    $selected = $def && $def == $value['regency_code'] ? ' selected="selected" ' : '';
                    if($mode == 0){
                        $regency .= '<option value="'.$value['regency_code'].'" '.$selected.'>'.$value['regency_name'].'</option>';
                    }elseif($mode == 1){
                        $regency .= '<option value="'.$value['regency_name'].'" data-code="'.$value['regency_code'].'" '.$selected.'>'.$value['regency_name'].'</option>';
                    }
                }
            }
            echo $regency;
        }
    }

    public function reloadsubdistrictAction(){
        $regency = $this->getRequest()->getParam('regency');
        $mode = $this->getRequest()->getParam('mo') ? $this->getRequest()->getParam('mo') : 0;
        $def = $this->getRequest()->getParam('dv') ? $this->getRequest()->getParam('dv') : '';
        if($regency){
            $subdistrict = '';
            $collection = Mage::getModel('ksdirectory/subdistrict')->getCollection();
            $collection->addFieldToFilter('regency_code', $regency);
            $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('subdistrict_code')->columns('subdistrict_name')->order('subdistrict_name', 'ASC');
            if($collection->getData()){
                if($mode == 0){
                    $subdistrict = '<option value="">'.Mage::helper('ksdirectory')->__('-- Please Select --').'</option>';
                }elseif($mode == 1){
                    $subdistrict = '<option value="" data-code="">'.Mage::helper('ksdirectory')->__('-- Please Select --').'</option>';
                }
                foreach ($collection->getData() as $key => $value) {
                    $selected = $def && $def == $value['subdistrict_code'] ? ' selected="selected" ' : '';
                    if($mode == 0){
                        $subdistrict .= '<option value="'.$value['subdistrict_code'].'" '.$selected.'>'.$value['subdistrict_name'].'</option>';
                    }elseif($mode == 1){
                        $subdistrict .= '<option value="'.$value['subdistrict_name'].'" data-code="'.$value['subdistrict_code'].'" '.$selected.'>'.$value['subdistrict_name'].'</option>';
                    }
                }
            }
            echo $subdistrict;
        }
    }
    
    public function reloadvillageAction(){
    	$subdistrict = $this->getRequest()->getParam('subdistrict');
    	if($subdistrict){
    		$village = '';
    		$collection = Mage::getModel('ksdirectory/village')->getCollection();
    		$collection->addFieldToFilter('subdistrict_code', $subdistrict);
    		$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('village_code')->columns('village_name')->order('village_name', 'ASC');
    		if($collection->getData()){
    			$village = '<option value="">'.Mage::helper('ksdirectory')->__('-- Please Select --').'</option>';
    			foreach ($collection->getData() as $key => $value) {
    				$village .= '<option value="'.$value['village_code'].'">'.$value['village_name'].'</option>';
    			}
    		}
    		echo $village;
    	}
    }
    
    public function reloadzipAction(){
    	$village = $this->getRequest()->getParam('village');
    	if($village){
    		$zip = '';
    		$collection = Mage::getModel('ksdirectory/village')->getCollection();
    		$collection->addFieldToFilter('village_code', $village);
    		$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('postcode');
    		if($collection->getData()){
    			foreach ($collection->getData() as $key => $value) {
    				$zip .= $value['postcode'];
    			}
    		}
    		echo $zip;
    	}
    }

    public function suggestionzipcodeAction(){
        $subdistrict = $this->getRequest()->getParam('subdistrict');
        $type = $this->getRequest()->getParam('type') ? trim($this->getRequest()->getParam('type')) : 'none';
        if($subdistrict){
            $collection = Mage::getModel('ksdirectory/village')->getCollection();
            $collection->addFieldToFilter('subdistrict_code', $subdistrict);
            $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('village_name')->columns('postcode')->order('postcode', 'ASC');//->distinct(true);
            $count = $collection->getSize();
            $zipcodes = '';
            if($collection->getData()){
                $zipcodes = '<ul>';
                $link = 'linkSuggestPostCode';
                switch ($type) {
                    case 'billing':
                        $link = 'linkSuggestPostCodeBilling';
                        break;
                    case 'shipping':
                        $link = 'linkSuggestPostCodeShipping';
                        break;    
                    default:
                        $link = 'linkSuggestPostCode';
                        break;
                }
                foreach ($collection->getData() as $key => $value) {
                    $zipcodes .= '<li><a href="javascript:void(0)" class="'.$link.'" data-val="'.$value['postcode'].'"><strong>'.$value['village_name'].'</strong>, '.$value['postcode'].'</a></li>';
                }
                $zipcodes .= '</ul>';
            }
            echo $zipcodes;
        }
    }

    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'index':
                return Mage::getSingleton('admin/session')->isAllowed('admin/ksall/ksdirectory/actions/view');
                break;
            case 'newprovince':
            case 'newregency':
            case 'newsubdistrict':
            case 'newvillage':
                return Mage::getSingleton('admin/session')->isAllowed('admin/ksall/ksdirectory/actions/add');
                break;
            case 'editprovince':
            case 'editregency':
            case 'editsubdistrict':
            case 'editvillage':
               return Mage::getSingleton('admin/session')->isAllowed('admin/ksall/ksdirectory/actions/edit');
                break;
            case 'saveprovince':
            case 'saveregency':
            case 'savesubdistrict':
            case 'savevillage':
                return (Mage::getSingleton('admin/session')->isAllowed('admin/ksall/ksdirectory/actions/add') || Mage::getSingleton('admin/session')->isAllowed('admin/ksall/ksdirectory/actions/edit'));
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('admin/ksall/ksdirectory');
                break;
        }
    }

}
