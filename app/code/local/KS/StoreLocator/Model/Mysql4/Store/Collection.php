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
 * KS_StoreLocator_Model_Mysql4_Store_Collection
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_StoreLocator_Model_Mysql4_Store_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('storelocator/store');
    }
    
    public function loadById($id){
        $this->addFieldToFilter('id', $id);
        return $this;
    }

    public function getHistory()
    {
    	$collection = $this->getData();
    	return @$collection[0]['history'];
    }
    
}