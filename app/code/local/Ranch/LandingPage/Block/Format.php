<?php
/*
 * @author Ryan Permana <rpermana@kemana.com>
 *
 * */
class Ranch_LandingPage_Block_Format extends Mage_Core_Block_Template
{

    public function __construct(){
        parent::__construct();
    }

    public function getWebsite()
    {
        $model = Mage::getModel('landingpage/website')->getCollection();
        $data = $model->getData();
        return $data;
    }

    public function getStoreRanch()
    {
        $model = Mage::getModel('landingpage/store')->getCollection();
        $model->addFieldToFilter('website_id', array('eq' => 1));
        $model->getSelect()
            //->reset(Zend_Db_Select::COLUMNS)
            ->join( array('c'=> 'ks_directory_regency'), 'c.regency_code = main_table.city');
        $data = $model->getData();
        return $data;
    }

    public function getStoreFarmer()
    {
        $model = Mage::getModel('landingpage/store')->getCollection();
        $model->addFieldToFilter('website_id', array('eq' => 3));
        $model->getSelect()
            //->reset(Zend_Db_Select::COLUMNS)
            ->join( array('c'=> 'ks_directory_regency'), 'c.regency_code = main_table.city');
        $data = $model->getData();
        return $data;
    }

}
