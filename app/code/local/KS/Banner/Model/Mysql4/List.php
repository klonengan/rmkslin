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
 * KS_Banner_Model_Mysql4_List
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */


class KS_Banner_Model_Mysql4_List extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('banner/list', 'id');
    }

    public function getAllBanners($type, $limit, $isActive){
    	return $this->_getAllBanners($type, $limit, $isActive);
    }

    protected function _getAllBanners($type = 1, $limit = 0, $isActive = true)
    {
    	$readDb = $this->_getReadAdapter();
    	$query = ' SELECT bl.*, ';
    	$query .= ' bt.width, bt.height ';
        $query .= ' FROM '.$this->getMainTable().' bl ';
    	$query .= ' LEFT JOIN '.$this->getTable('banner/type').' bt ON bl.type_id = bt.id ';

    	if($isActive) $query .= ' WHERE bl.active = 1 AND bl.type_id = '.$type;
        $query .= ' ORDER BY bl.sort_order ASC ';
        if($limit > 0) $query .= ' LIMIT '.$limit;
    	$result = $readDb->fetchAll($query);
    	if(!empty($result))
            return $result;
        else
            return false;
    }

}