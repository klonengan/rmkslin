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
 * Source Model KS_Banner_Model_Source_Type
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_Banner_Model_Source_Category
{
	public function toOptionArray($useHeader = TRUE)
	{
		$options = array();
		if($useHeader) $options[''] = Mage::helper('banner')->__('--- Select a banner category ---');

		$categories = Mage::getModel('banner/list')->getCategoryList();
		foreach ($categories as $category){
	    	$options[$category['value']] = $category['label'];
    	}
		return $options;
	}
}
