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
 * KS_Banner_Model_List
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_Banner_Model_List extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('banner/list');
    }

    public function getAllBanners($type, $limit, $isActive){
    	return $this->getResource()->getAllBanners($type, $limit, $isActive);
    }

    public function getCategory(){

        $categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('*')//or you can just add some attributes
            ->addAttributeToFilter('level', 2)//2 is actually the first level
            ->addAttributeToFilter('is_active', 1)//if you want only active categories
            ->addAttributeToFilter('include_in_menu', 1)
        ;

        $_categories = $categories;

        $data = array(array('value'=> -1, 'label' => '-- Select Category --'));

        if (!empty($_categories))
        {
            foreach ($_categories as $_category)
            {
                $data[] = array('value'=>$_category->getId(), 'label'=>$_category->getName());
            }
        }

        return $data;
    }

    public function getCategoryName($catId){

        $categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('*')//or you can just add some attributes
            ->addAttributeToFilter('entity_id', $catId )//2 is actually the first level
            ->addAttributeToFilter('level', 2)//2 is actually the first level
            ->addAttributeToFilter('is_active', 1)//if you want only active categories
            ->addAttributeToFilter('include_in_menu', 1)
        ;
        //echo "<pre>";var_dump($categories->getData());
        //$data = array();
        if (!empty($_categories))
        {
            foreach ($_categories as $_category)
            {
                $data[] = array('value'=>$_category->getId(), 'label'=>$_category->getName());
            }

            return $data;
        }


    }

    public function getCategoryList(){

        $categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('*')//or you can just add some attributes
            ->addAttributeToFilter('level', 2)//2 is actually the first level
            ->addAttributeToFilter('is_active', 1)//if you want only active categories
            ->addAttributeToFilter('include_in_menu', 1)
        ;
        $_categories = $categories;
        if (!empty($_categories))
        {
            foreach ($_categories as $_category)
            {
                $data[] = array('value'=>$_category->getId(), 'label'=>$_category->getName());
            }
        }

        return $data;
    }

}
