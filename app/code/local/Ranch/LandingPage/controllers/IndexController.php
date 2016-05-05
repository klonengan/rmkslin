<?php
/**
 * Created by PhpStorm.
 * User: ryan <rpermana@kemana.com>
 * Date: 1/5/2016
 * Time: 10:55 AM
 */
class Ranch_LandingPage_IndexController extends Mage_Core_Controller_Front_Action  {

    public function _construct()
    {
        parent::_construct();
    }

    public function indexAction(){

        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveStoreAction()
    {
        $city       = $this->getRequest()->getPost('city');
        $storepost    = $this->getRequest()->getPost('store');
        $brand      = $this->getRequest()->getPost('brand');

        if( $storepost == '' ) $storeid = 1;

        setcookie(KS_Price_Helper_Data::COOKIE_NAME, $storeid, time()+ KS_Price_Helper_Data::COOKIE_TIME_LENGTH, "/");
        $this->_redirectUrl(Mage::getBaseUrl());

    }


    /* load all store for brand */
    public function loadStoreAction()
    {
        $citycode   = $this->getRequest()->getPost('city');
        $brandid    = $this->getRequest()->getPost('brand_id');

        $model = Mage::getModel('landingpage/store')->getCollection();
        $model->addFieldToFilter('website_id', array('eq' => $brandid));
        $model->addFieldToFilter('status', array('eq' => 1));

        if( $citycode != '0' ) {
            $model->addFieldToFilter('city', array('eq' => $citycode));
        }

        $model->getSelect()
            ->join( 'ks_directory_regency', 'ks_directory_regency.regency_code = city', array()  );

        //$model->printLogQuery(true);
        $stores = $model->getData();
        $response['status'] = 'error';
        $response['html'] = '<option value="">---</option>';

        $result = '<option value="0">'.$this->__('All Store').'</option>';
        foreach ($stores as $key => $value) {
            $result .= '<option value="'.$value['id'].'" data-code="'.$value['id'].'">'.$value['name'].'</option>';
        }

        $response['status'] = 'success';
        $response['return'] = $stores;
        $response['html']   = $result;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

    }

    /* load city for brand  */
    public function loadCityAction()
    {
        $brandid    = $this->getRequest()->getPost('brand_id');
        $model = Mage::getModel('landingpage/store')->getCollection();
        $model->addFieldToFilter('website_id', array('eq' => $brandid));
        $model->addFieldToFilter('status', array('eq' => 1));
        $model->getSelect()->join( array('c'=> 'ks_directory_regency'), 'c.regency_code = main_table.city')->group('city');
        $cities = $model->getData();
        //$model->printLogQuery(true);

        $response['status'] = 'error';
        $response['html'] = '<option value="">---</option>';


        $result = '<option value="0">'.$this->__('All City').'</option>';
        /*$result .= '<option value="0">'.$this->__('Show current location').'</option>';*/
        foreach ($cities as $key => $value) {
            $result .= '<option  value="'.$value['regency_code'].'" data-code="'.$value['regency_code'].'">'.$value['regency_name'].'</option>';
        }

        $response['status'] = 'success';
        $response['html']   = $result;

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

    }

    /* load city for brand  */
    public function loadGeoCityAction()
    {
        $cityname     = $this->getRequest()->getPost('city');
        $resource       = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query          = " SELECT * FROM ks_directory_regency WHERE regency_name LIKE '%$cityname%'";
        $results        = $readConnection->fetchAll($query);

        $response['status'] = 'success';
        $response['kodecity'] = $results;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /* load city for header.phtml */
    public function loadCityHeaderAction()
    {
        $session = Mage::getSingleton('core/session')->getStoreInfo();

        //$brandid    = $this->getRequest()->getPost('brand_id');
        $model = Mage::getModel('landingpage/store')->getCollection();
        $model->addFieldToFilter('brand_id', array('eq' => $session['selected']['brand_id']));
        $model->getSelect()->join( array('c'=> 'ks_directory_regency'), 'c.regency_code = main_table.city')->group('city');

        $cities = $model->getData();

        $response['status'] = 'error';
        $response['html'] = '<option value="">---</option>';

        $result = '<option value="0">'.$this->__('All City').'</option>';
        foreach ($cities as $key => $value) {
            $result .= '<option  value="'.$value['regency_code'].'" data-code="'.$value['regency_code'].'">'.$value['regency_name'].'</option>';
        }

        $response['status'] = 'success';
        $response['html']   = $result;

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }


    /* load store for header.phtml */
    public function loadStoreHeaderAction()
    {
        //$session = Mage::getSingleton('core/session')->getStoreInfo();

        $citycode   = $this->getRequest()->getPost('city');
        $brandid    = $this->getRequest()->getPost('brand_id');

        $model = Mage::getModel('landingpage/store')->getCollection();
        $model->addFieldToFilter('brand_id', array('eq' => $brandid));

        if( $citycode != '0' ) {
            $model->addFieldToFilter('city', array('eq' => $citycode));
        }

        $model->getSelect()->join( array('c'=> 'ks_directory_regency'), 'c.regency_code = main_table.city');
        $stores = $model->getData();

        $response['status'] = 'error';
       /* foreach ($stores as $key => $value) {
            $result[] = $value;
        }*/

        $image_url =  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'store/brand/logo-01.png';

        $result  = "<div class=\"row\">";
        foreach( $stores as $store => $value ):
            //if($value['city'] == $session['selected']['regency_id'] ):
            $result  .= "<div class=\"col-xs-6 col-md-6\">";
            $result  .= "<a href=\"#\" class=\"thumbnail\"><img src=\"".$image_url."\" alt=\"\"></a>";
            $result  .= "<center><span>".$value['name']."</span></center>";
            $result  .= "</div>";
            //endif;
        endforeach;
        $result   .= "</div>";
        $response['status'] = 'success';
        $response['html']   = $result;

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

    }


}