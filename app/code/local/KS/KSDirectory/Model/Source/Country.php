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
 * @package     KS_KSDirectory
 * @copyright   Copyright (c) 2014 Kemana Services (http://www.kemanaservices.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Source Model KS_KSDirectory_Model_Source_Country
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_KSDirectory_Model_Source_Country
{

    public function toOptionArray($type = 0)
    {
        $options = array();
        if($type == 0)
        {
            $options = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }
        elseif($type == 1)
        {
            $collection = Mage::getModel('ksdirectory/province')->getCollection();
            $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('country_code');
            $collection->getSelect()->group('country_code')->order('country_code', 'ASC');
            if($collection->getData()){
                $options[''] = '-- Please Select --';
                foreach ($collection->getData() as $value) {
                    $options[$value['country_code']] = Mage::app()->getLocale()->getCountryTranslation($value['country_code']);
                }
            }
        }

        return $options;
    }
}
