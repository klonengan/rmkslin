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
 * Block KS_Banner_Block_Adminhtml_Banner_Grid_Column_Renderer_Image
 * Bank Name renderer
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */

class KS_Banner_Block_Adminhtml_Banner_Grid_Column_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$urlMedia = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'wysiwyg/banner/';
    	$image = $row->getImage();
    	$typeId = $row->getTypeId();
    	$imageUrl = $urlMedia.str_pad($typeId, 3, '0', STR_PAD_LEFT).'/'.$image;
    	if($image){
    		return '<a onclick="imagePreview(\'image_'.$row->getId().'\'); return false;" href="'.$imageUrl.'"><img id="image_'.$row->getId().'" src="'.$imageUrl.'" width="200px" /></a><br><span>'.$image.'</span>';
    	}
    	return 'No Image';
    }

}
