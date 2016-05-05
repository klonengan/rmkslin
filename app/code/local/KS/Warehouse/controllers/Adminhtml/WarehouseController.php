<?php
class KS_Warehouse_Adminhtml_WarehouseController extends Mage_Adminhtml_Controller_Action {
		
	/*
	* Permission User Club
	*/
	public function gridPermissionWarehouseAction()
    {
        $id = $this->getRequest()->getParam('user_id');
        $model = Mage::getModel('admin/user');
        if ($id) {
            $model->load($id);
        }

        Mage::register('permissions_user', $model);
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('kswarehouse/adminhtml_permissions_user_edit_tab_warehouse')->toHtml()
        );

    }
	
		
}

