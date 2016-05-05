<?php

/**
 * Class Gaboli_Warehouse_Model_Location
 */
class KS_Warehouse_Model_Location extends Mage_Core_Model_Abstract
{
    /**
     * Init model
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('ks_warehouse/location');
    }
	
	/*
	* 	toOptionArray()
	*/
	public function toOptionArray($select = false){
		$options = array();
		if(!$select)
		$options[] = array('value' => '', 'label' => Mage::helper('kswarehouse')->__('-Please Select-'));
		$collection = $this->getCollection();
		foreach($collection as $_warehouse){
			$options[] = array('value' => $_warehouse->getId(), 'label' => $_warehouse->getName());
		}
		
		return $options;
		
	}

    /*
	* 	toOptionArray()
	*/
    public function toOptionArraySwitch($select = false, $websiteid = null ){
        $options = array();
        if(!$select)
            $options[] = array('value' => '', 'label' => Mage::helper('kswarehouse')->__('-Please Select-'));
        $collection = $this->getCollection();
        $collection->addFieldToFilter('website_id', array('eq' => $websiteid));

        foreach($collection as $_warehouse){
            $options[] = array('value' => $_warehouse->getId(), 'label' => $_warehouse->getName());
        }

        return $options;

    }


}


