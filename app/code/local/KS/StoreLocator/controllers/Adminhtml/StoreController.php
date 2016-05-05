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
 * Index controller
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_StoreLocator_Adminhtml_StoreController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction()
    {
		$this->loadLayout()
			->_setActiveMenu('storelocator/store')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }

    public function indexAction()
    {
        if($this->_isActionAllowed('view')){
            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('storelocator/adminhtml_store'));
            $this->renderLayout();
        }
    }

    public function newAction()
    {
        if($this->_isActionAllowed('add')){

            $_model = Mage::getModel('storelocator/store');
            Mage::register('storelocator_data', $_model);

            $this->_initAction();
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('storelocator/adminhtml_store_edit'))
                    ->_addLeft($this->getLayout()->createBlock('storelocator/adminhtml_store_edit_tabs'));

            $this->renderLayout();
        }
    }

    public function editAction()
    {
        if($this->_isActionAllowed('edit')){

            $id = $this->getRequest()->getParam('id');

            $_model = Mage::getModel('storelocator/store')->load($id);

            $this->_title($this->__('Edit Store Data'));

            Mage::register('storelocator_data', $_model);

            $this->_initAction();
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('storelocator/adminhtml_store_edit'))
                    ->_addLeft($this->getLayout()->createBlock('storelocator/adminhtml_store_edit_tabs'));

            $this->renderLayout();
        }
    }

    public function saveAction()
    {
        if ($arrData = $this->getRequest()->getPost()) 
        {
            $_model = Mage::getModel('storelocator/store');
            $_model->setData($arrData);
            $id = $this->getRequest()->getParam('id');

            try 
            {
                $currentDate = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
                $admin = Mage::getSingleton('admin/session');
                $adminData = $admin->getUser()->getUserId().'|'.$admin->getUser()->getUsername().'|'.$admin->getUser()->getFirstname().'|'.$admin->getUser()->getLastname().'|'.$admin->getUser()->getEmail();
                if($id){
                    $histories = $_model->getCollection()->loadById($id)->getHistory();
                    if($histories){
                        $history = unserialize($histories);
                        $history['Updated'][$currentDate]['admin'] = $adminData;
                        $_model->setHistory(serialize($history));
                    }
                    $_model->setId($id);
                }
                else
                {
                    $history['Added'][$currentDate]['admin'] = $adminData;
                    $_model->setHistory(serialize($history)); 
                }

                $_model->save();

                if($id){
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storelocator')->__('Store data has been updated successfully.'));
                }
                else{
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storelocator')->__('New store has been added successfully.'));
                }
                
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $_model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($arrData);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }

        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storelocator')->__('Unable to save the store data'));
        $this->_redirect('*/*/');
    }

    /* This function is not really deleted, just hide it from user. */
    public function deleteAction()
    {
        if($this->_isActionAllowed('delete'))
        {
            $_model = Mage::getModel('storelocator/store');
            $_model->setData($arrData);
            $id = $this->getRequest()->getParam('id');

            try 
            {
                $currentDate = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
                $admin = Mage::getSingleton('admin/session');
                $adminData = $admin->getUser()->getUserId().'|'.$admin->getUser()->getUsername().'|'.$admin->getUser()->getFirstname().'|'.$admin->getUser()->getLastname().'|'.$admin->getUser()->getEmail();
                if($id){
                    $histories = $_model->getCollection()->loadById($id)->getHistory();
                    if($histories){
                        $history = unserialize($histories);
                        $history['Deleted'][$currentDate]['admin'] = $adminData;
                        $_model->setHistory(serialize($history));
                    }
                    $_model->setId($id);
                    //$_model->setSuperHide(1);
                    $_model->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storelocator')->__('Store data has been deleted successfully.'));
                    Mage::getSingleton('adminhtml/session')->setFormData(false);

                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $_model->getId()));
                        return;
                    }
                    $this->_redirect('*/*/');
                }
                else
                {
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storelocator')->__('New store has been added successfully.'));
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($arrData);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                }
                return;

            } catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($arrData);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }

        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storelocator')->__('Unable to save the store data'));
        $this->_redirect('*/*/');
    }

    public function reloadcityAction(){
        $region = $this->getRequest()->getParam('region');
        if($region){
            $collection = Mage::getModel('ksdirectory/city')->getCollection();
            $collection->addFieldToFilter('province_name', $region);
            $collection->getSelect()->order('city_name', 'ASC');
            $city = '<option value="">'.Mage::helper('storelocator')->__('--- Select a City ---').'</option>';
            foreach ($collection as $_city) {
                $city .= '<option value="'.$_city['city_name'].'">'.$_city['city_name'].'</option>';
            }
            echo $city;
        }
        
    }

    protected function _isActionAllowed($action)
    {
        try {
            $session = Mage::getSingleton('admin/session');
            $resourceLookup = "admin/ksall/storelocator/actions/{$action}";
            $resourceId = $session->getData('acl')->get($resourceLookup)->getResourceId();
            if (!$session->isAllowed($resourceId)) {
                throw new Exception('');
            }
            return true;
        }
        catch (Exception $e) {
            $this->_forward('denied');
            return false;
        }
    }

}
