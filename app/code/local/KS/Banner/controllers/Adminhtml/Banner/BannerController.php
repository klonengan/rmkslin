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
 * @package     KS_Banner
 * @copyright   Copyright (c) 2014 Kemana Services (http://www.kemanaservices.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Index controller
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_Banner_Adminhtml_Banner_BannerController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction()
    {
		$this->loadLayout()
			->_setActiveMenu('ksall')
			->_addBreadcrumb(Mage::helper('banner')->__('Banner Management'), Mage::helper('banner')->__('Banner Management'));
        return $this;
    }

    public function indexAction()
    {
        $this->_redirect('*/*/list');
    }

    public function listAction()
    {
        if($this->_isAllowed()){
            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('banner/adminhtml_banner'));
            $this->renderLayout();
        }
    }

    public function addbannerAction()
    {
        if($this->_isAllowed()){

            $_model = Mage::getModel('banner/list');
            Mage::register('banner_list_data', array());

            $this->_initAction();
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('banner/adminhtml_banner_edit'))
                    ->_addLeft($this->getLayout()->createBlock('banner/adminhtml_banner_edit_tabs'));

            $this->renderLayout();
        }
    }

    public function editbannerAction()
    {
        if($this->_isAllowed()){

            $id = $this->getRequest()->getParam('id');
            $collection = Mage::getModel('banner/list')->getCollection();
            $collection->addFieldToFilter('main_table.id', $id);
            $collection->getSelect()->join(array('type' => Mage::getSingleton('core/resource')->getTableName('banner/type')), 'main_table.type_id= type.id', array('type.width', 'type.height'));
            Mage::register('banner_list_data', $collection->getData());

            $this->_initAction();
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('banner/adminhtml_banner_edit'))
                    ->_addLeft($this->getLayout()->createBlock('banner/adminhtml_banner_edit_tabs'));

            $this->renderLayout();
        }
    }

    public function savebannerAction()
    {

        if($this->_isAllowed()){
            if($arrData = $this->getRequest()->getPost())
            {
                if($arrData['type_id'] != ''){
                    $currentTime =  date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
                    // Mode Add Data
                    if(! array_key_exists('id', $arrData)){
                        $image = $this->_uploadImage('imagebanner', str_pad($arrData['type_id'], 3, '0', STR_PAD_LEFT));
                        $banner = Mage::getModel('banner/list');
                        $banner->setTypeId($arrData['type_id']);
                        $banner->setImage($image['image']);
                        $banner->setSortOrder($arrData['sort_order']);
                        $banner->setCreatedAt($currentTime);
                        $banner->setUpdatedAt($currentTime);
                        $banner->setAdminId(Mage::getSingleton('admin/session')->getUser()->getUserId());
                        $banner->setActive($arrData['active']);
                        $banner->setCategoryId($arrData['category_id']);
                        $banner->setTargetUrl($arrData['target_url']);
                        $banner->save();
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('banner')->__('New Banner has been added successfully.'));
                    }
                    else{ // Edit Mode
                        // Main Data
                        if(array_key_exists('id', $arrData)){
                            $model = Mage::getModel('banner/list');
                            $oldData = $model->load($arrData['id']);
                            $imageFile = $oldData->getImage();
                            $arrData['image'] = $imageFile;

                            // If folder location same
                            if($oldData->getTypeId() == $arrData['type_id']){
                                if(array_key_exists('delete', $arrData['imagebanner'])){
                                    if($this->_deleteImage($imageFile, str_pad($oldData->getTypeId(), 3, '0', STR_PAD_LEFT))){
                                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('banner')->__("Image '%s' have been deleted successfully.", $imageFile));
                                        $arrData['image'] = '';
                                    }
                                }
                                if($_FILES['imagebanner']['name'] != ''){
                                    $this->_deleteImage($imageFile, str_pad($oldData->getTypeId(), 3, '0', STR_PAD_LEFT));
                                    $image = $this->_uploadImage('imagebanner', str_pad($arrData['type_id'], 3, '0', STR_PAD_LEFT));
                                    if($image) $arrData['image'] = $image['image'];
                                }
                            }
                            // If folder location not same
                            else{
                                if($_FILES['imagebanner']['name'] != ''){
                                    $this->_deleteImage($imageFile, str_pad($oldData->getTypeId(), 3, '0', STR_PAD_LEFT));
                                    $image = $this->_uploadImage('imagebanner', str_pad($arrData['type_id'], 3, '0', STR_PAD_LEFT));
                                    if($image) $arrData['image'] = $image['image'];
                                }
                                else{
                                    if(array_key_exists('delete', $arrData['imagebanner'])){
                                        $this->_deleteImage($imageFile, str_pad($oldData->getTypeId(), 3, '0', STR_PAD_LEFT));
                                        $arrData['image'] = '';
                                    }
                                    else{
                                        $source = Mage::getBaseDir('media') . DS . 'wysiwyg'. DS . 'banner' . DS . str_pad($oldData->getTypeId(), 3, '0', STR_PAD_LEFT) . DS .$imageFile;
                                        $destFolder = Mage::getBaseDir('media') . DS . 'wysiwyg'. DS . 'banner' . DS . str_pad($arrData['type_id'], 3, '0', STR_PAD_LEFT) . DS;
                                        $extFile = pathinfo($imageFile, PATHINFO_EXTENSION);
                                        $baseName = basename($imageFile, '.'.$extFile);
                                        $newFilename = $imageFile;

                                        $index = 1;
                                        while( file_exists($destFolder . $newFilename) ) {
                                            $newFilename = $baseName. '_' . $index . '.' . $extFile;
                                            $index ++;
                                        }
                                        rename($source, $destFolder.$newFilename);
                                    }
                                }

                            }

                            $model->setData($arrData);
                            $model->save();
                        }

                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('banner')->__('Banner has been updated successfully.'));
                    }
                }
            }
        }
        $this->_redirect('*/*/list');
    }

    public function deletebannerAction()
    {
        if($this->_isAllowed()){
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('banner/list');
            $banner = $model->load($id);
            $typeId = $banner->getTypeId();
            $image = $banner->getImage();
            $model->setId($id);
            $model->delete();
            $imageFolder = str_pad($typeId, 3, '0', STR_PAD_LEFT);
            $this->_deleteImage($image, $imageFolder);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('banner')->__('Banner has been deleted successfully.'));
        }
        $this->_redirect('*/*/list');
    }

    public function typeAction()
    {
        if($this->_isAllowed()){
            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('banner/adminhtml_type'));
            $this->renderLayout();
        }
    }

    public function addtypeAction()
    {
        if($this->_isAllowed()){

            $_model = Mage::getModel('banner/type');
            Mage::register('banner_type_data', array());

            $this->_initAction();
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('banner/adminhtml_type_edit'))
                    ->_addLeft($this->getLayout()->createBlock('banner/adminhtml_type_edit_tabs'));

            $this->renderLayout();
        }
    }

    public function edittypeAction()
    {
        if($this->_isAllowed()){

            $id = $this->getRequest()->getParam('id');
            $mainData = Mage::getModel('banner/type')->load($id);
            Mage::register('banner_type_data', $mainData);

            $this->_initAction();
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('banner/adminhtml_type_edit'))
                    ->_addLeft($this->getLayout()->createBlock('banner/adminhtml_type_edit_tabs'));

            $this->renderLayout();
        }
    }

    public function savetypeAction()
    {
        if($this->_isAllowed()){
            if($arrData = $this->getRequest()->getPost())
            {
                // Mode Add Data
                if(! array_key_exists('id', $arrData)){
                    $type = Mage::getModel('banner/type');
                    $type->setName($arrData['name']);
                    $type->setWidth($arrData['width']);
                    $type->setHeight($arrData['height']);
                    $type->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('banner')->__('New Banner type has been added successfully.'));
                }
                else{ // Edit Mode
                    // Main Data
                    if(array_key_exists('id', $arrData)){
                        $type = Mage::getModel('banner/type');
                        $type->setData($arrData);
                        $type->save();
                    }

                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('banner')->__('Banner type has been updated successfully.'));
                }
            }
        }
        $this->_redirect('*/*/type');
    }

    public function updateresolutionAction(){
        $typeId = $this->getRequest()->getParam('type');
        if($typeId){
            $model = Mage::getModel('banner/type')->load($typeId);
            if($typeId == 1){
                echo 'The width of this image must be exactly '.$model->getWidth().'px and for the height can be adapted according the needs.';
            }
            else{
                echo 'The resolution of this image must be exactly '.$model->getWidth().'px X '.$model->getHeight().'px (W x H)';
            }
            
        }
        echo '';
    }

    protected function _uploadImage($imageField, $folder){
        if($imageField != '' && isset($_FILES[$imageField]['name']) && $_FILES[$imageField]['name'] != '') {
            $data['image'] = '';
            try{
                $path = Mage::getBaseDir('media') . DS . 'wysiwyg'. DS . 'banner' . DS . $folder . DS;

                $uploader = new Varien_File_Uploader($imageField);
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); 
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                
                $newFilename = $uploader->getNewFileName($path.strtolower(str_replace(' ', '_', $_FILES[$imageField]['name'])));
                $uploader->save($path, $newFilename);
                $data['image'] = $newFilename;
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('banner')->__("Image '%s' have been uploaded successfully.", $newFilename));
            } 
            catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            return $data;
        }
        return;
    }

    protected function _deleteImage($imageFile, $folder){
        $path = Mage::getBaseDir('media') . DS . 'wysiwyg'. DS . 'banner' . DS . $folder . DS;
        $callback = unlink($path.$imageFile);
        return $callback;
    }

    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());

        switch ($action) {
            case 'index':
            case 'list':
                return Mage::getSingleton('admin/session')->isAllowed('admin/ksall/banner/banner_list/view');
                break;
            case 'addbanner':
                return Mage::getSingleton('admin/session')->isAllowed('admin/ksall/banner/banner_list/add');
                break;
            case 'editbanner':
                return Mage::getSingleton('admin/session')->isAllowed('admin/ksall/banner/banner_list/edit');
                break;
            case 'savebanner':
                return (Mage::getSingleton('admin/session')->isAllowed('admin/ksall/banner/banner_list/add') || (Mage::getSingleton('admin/session')->isAllowed('admin/ksall/banner/banner_list/edit')));
                break;
            case 'deletebanner':
                return Mage::getSingleton('admin/session')->isAllowed('admin/ksall/banner/banner_list/delete');
                break;
            case 'type':
                return Mage::getSingleton('admin/session')->isAllowed('admin/ksall/banner/banner_type/view');
                break;
            case 'addtype':
                return Mage::getSingleton('admin/session')->isAllowed('admin/ksall/banner/banner_type/add');
                break;
            case 'edittype':
                return Mage::getSingleton('admin/session')->isAllowed('admin/ksall/banner/banner_type/edit');
                break;
            case 'savetype':
                return (Mage::getSingleton('admin/session')->isAllowed('admin/ksall/banner/banner_type/add') || (Mage::getSingleton('admin/session')->isAllowed('admin/ksall/banner/banner_type/edit')));
                break;    
            default:
                return Mage::getSingleton('admin/session')->isAllowed('admin/ksall/banner');
                break;
        }
    }

}
