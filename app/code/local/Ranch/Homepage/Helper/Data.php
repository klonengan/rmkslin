<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 3/1/2016
 * Time: 2:34 PM
 */

class Ranch_Homepage_Helper_Data extends Mage_Core_Helper_Abstract {

    public function mostviewed()
    {
        $totalPerPage = 5;
        $storeId = Mage::app()->getStore()->getId();
        $_products  = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('visibility', array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH
            ))
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->addViewsCount()
            ->setPageSize($totalPerPage);
        $result = $_products->getData();
        return $result;
    }

    public function highlighthome()
    {
        $entity     = 'catalog_product';
        $code       = 'highlight';
        $attr       = Mage::getResourceModel('catalog/eav_attribute')->loadByCode($entity,$code);

        $storeId    = Mage::app()->getStore()->getId();
        if ($attr->getId()) :
            //$attr_id = $attr['default_value'];
            $products = Mage::getModel('catalog/product')->getCollection();
            $products->addAttributeToSelect('*');
            $products->addStoreFilter($storeId);
            $products->addAttributeToFilter('highlight', array( 'notnull'=>true ));
            $products->setPageSize(4);

        endif;
        return $products;
    }

}