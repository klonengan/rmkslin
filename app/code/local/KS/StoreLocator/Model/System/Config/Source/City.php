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
 * KS_StoreLocator_Model_System_Config_Source_Region
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */


class KS_StoreLocator_Model_System_Config_Source_City
{
    protected $_options;

    public function toOptionArray($region)
    {
        if (!$this->_options && $region != '') {

            $collection = Mage::getModel('ksdirectory/city')->getCollection();
            $collection->addFieldToFilter('province_name', $region);
            $collection->getSelect()->order('city_name', 'ASC');
            $this->_options = array();
            $this->_options[] = array('label' => '--- Select a City ---', 'value' => '');
            foreach ($collection as $city) {
                $this->_options[] = array('label' => $city['city_name'], 'value' => $city['city_name']);
            }
        }
        $options = $this->_options;
        return $options;
    }
}
