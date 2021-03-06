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
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_Banner_Model_Source_Transition
{
   public function toOptionArray()
    {
        return array(
            array('value' => 'slide', 'label'=>Mage::helper('adminhtml')->__('Slide Effect')),
            array('value' => 'fade', 'label'=>Mage::helper('adminhtml')->__('Fade Effect ')),
            array('value' => 'cube', 'label'=>Mage::helper('adminhtml')->__('Cube Effect ')),
            array('value' => 'coverflow', 'label'=>Mage::helper('adminhtml')->__('Coverflow Effect '))
        );
    }
}
