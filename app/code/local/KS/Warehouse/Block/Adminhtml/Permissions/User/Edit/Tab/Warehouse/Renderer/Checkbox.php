<?php
/*--------------------------------------------------------------------------
*
*   Modul KS_Club
*   Version 0.1.0
*   Created December 22, 2015
*   Developed by Didi Kusnadi (jalapro08@gmail.com)
*   Copyright © kemana.com - 2015
*
--------------------------------------------------------------------------*/
class KS_Warehouse_Block_Adminhtml_Permissions_User_Edit_Tab_Warehouse_Renderer_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox{

    protected function _getCheckboxHtml($value, $checked)
    {
        $html = '<input type="checkbox" ';
        $html .= 'name="' . $this->getColumn()->getFieldName() . '" ';
        $html .= 'value="' . $this->escapeHtml($value) . '" ';
        $html .= 'class="'. ($this->getColumn()->getInlineCss() ? $this->getColumn()->getInlineCss() : 'checkbox') .'" ';
        $html .= 'onchange="setUnset(this,\''.$this->getColumn()->getUnsetItem().'\',\''.$this->getColumn()->getSetItem().'\')" ';
        $html .= 'id="'. $this->getColumn()->getPrefixIdentifier() . $this->escapeHtml($value) . '" ';
        $html .= $checked . '/>';
        return $html;
    }
}