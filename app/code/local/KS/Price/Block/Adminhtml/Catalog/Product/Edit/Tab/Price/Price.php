<?php
class KS_Price_Block_Adminhtml_Catalog_Product_Edit_Tab_Price_Price
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group_Abstract
{

    /**
     * Initialize block
     */
    public function __construct()
    {
        $this->setTemplate('ksprice/product/edit/price/price.phtml');
    }

    /**
     * Retrieve list of initial customer groups
     *
     * @return array
     */
    protected function _getInitialCustomerGroups()
    {
        return array(Mage_Customer_Model_Group::CUST_GROUP_ALL => Mage::helper('catalog')->__('ALL GROUPS'));
    }

    /**
     * Sort values
     *
     * @param array $data
     * @return array
     */
    protected function _sortValues($data)
    {
        usort($data, array($this, '_sortTierPrices'));
        return $data;
    }

    /**
     * Sort tier price values callback method
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortTierPrices($a, $b)
    {
        if ($a['website_id'] != $b['website_id']) {
            return $a['website_id'] < $b['website_id'] ? -1 : 1;
        }
        if ($a['cust_group'] != $b['cust_group']) {
            return $this->getCustomerGroups($a['cust_group']) < $this->getCustomerGroups($b['cust_group']) ? -1 : 1;
        }
        if ($a['price_qty'] != $b['price_qty']) {
            return $a['price_qty'] < $b['price_qty'] ? -1 : 1;
        }

        return 0;
    }

    /**
     * Prepare global layout
     * Add "Add tier" button to layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('catalog')->__('Add Warehouse Price'),
                'onclick' => 'return warehousePriceControl.addItem()',
                'class' => 'add'
            ));
        $button->setName('add_warehouse_price_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }
	
	public function getWarehouse(){
		return Mage::getModel('ks_warehouse/location')->toOptionArray();
	}

	public function getValues(){
		return Mage::getModel('ksprice/price')->getWarehousePrices($this->getProduct());
	}
	
}
