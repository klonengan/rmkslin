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
 * KS_StoreLocator_Block_Adminhtml_Store_Grid_Column_Renderer_Operations
 * Store Operations renderer
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_StoreLocator_Block_Adminhtml_Store_Grid_Column_Renderer_Operations extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	return str_replace('|', '<br>', $row->getStoreOperations());
    }

}
