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
 * KS_KSDirectory_Block_Adminhtml_Directory_Grid
 *
 * @author      Edi Suryadi <esuryadi@kemanaservices.com>
 */
 
class KS_KSDirectory_Block_Adminhtml_Directory_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ksdirectory_directory_grid');
        $this->setUseAjax(false);
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        if(Mage::helper('ksdirectory')->isProvinceEnable()){
            $collection = Mage::getModel('ksdirectory/grid')->getCollection();
            if(Mage::helper('ksdirectory')->isRegencyEnable()){
                if(Mage::helper('ksdirectory')->isSubdistrictEnable()){
                    if(! Mage::helper('ksdirectory')->isVillageEnable()){
                        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                                                ->columns('id')
                                                ->columns('country_code')
                                                ->columns('province_code')
                                                ->columns('province_name')
                                                ->columns('regency_code')
                                                ->columns('regency_name')
                                                ->columns('subdistrict_code')
                                                ->columns('subdistrict_name')
                                                ->group('subdistrict_code');
                    }
                }
                else{
                    $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                                            ->columns('id')
                                            ->columns('country_code')
                                            ->columns('province_code')
                                            ->columns('province_name')
                                            ->columns('regency_code')
                                            ->columns('regency_name')
                                            ->group('regency_code');
                }
            }
            else{
                $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                                        ->columns('id')
                                        ->columns('country_code')
                                        ->columns('province_code')
                                        ->columns('province_name')
                                        ->group('province_code');
            }
        }
        else{
            $collection = null;
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $showAction = true;
        if(Mage::helper('ksdirectory')->isProvinceEnable()){

            // $this->addColumn('id', array(
            //     'header'    => Mage::helper('ksdirectory')->__('ID'),
            //     'align'     =>'left',
            //     'type'      => 'number',
            //     'index'     => 'id'
            // ));

            $this->addColumn('country_code', array(
                'header'    => Mage::helper('ksdirectory')->__('Country'),
                'align'     =>'left',
                'width'     => '50px',
                'index'     => 'country_code'
            ));

            $this->addColumn('province_name', array(
                'header'    => Mage::helper('ksdirectory')->__('Province'),
                'align'     =>'left',
                'index'     => 'province_name',
                'type'      => 'options',
                'options'   => Mage::getModel('ksdirectory/source_province')->toOptionArray(0),
                'renderer'  => 'KS_KSDirectory_Block_Adminhtml_Directory_Grid_Column_Renderer_Province'
            ));

            if(Mage::helper('ksdirectory')->isRegencyEnable()){
                $this->addColumn('regency_name', array(
                    'header'    => Mage::helper('ksdirectory')->__('Regency / City'),
                    'align'     =>'left',
                    'index'     => 'regency_name',
                    'renderer'  => 'KS_KSDirectory_Block_Adminhtml_Directory_Grid_Column_Renderer_Regency'
                    // 'type'      => 'options',
                    // 'options'   => Mage::getModel('ksdirectory/source_regency')->toOptionArray(0)
                ));

                if(Mage::helper('ksdirectory')->isSubdistrictEnable()){
                    $this->addColumn('subdistrict_name', array(
                        'header'    => Mage::helper('ksdirectory')->__('Sub-District'),
                        'align'     =>'left',
                        'index'     => 'subdistrict_name',
                        'renderer'  => 'KS_KSDirectory_Block_Adminhtml_Directory_Grid_Column_Renderer_Subdistrict'
                    ));

                    if(Mage::helper('ksdirectory')->isVillageEnable()){
                        $this->addColumn('village_name', array(
                            'header'    => Mage::helper('ksdirectory')->__('Village'),
                            'align'     =>'left',
                            'index'     => 'village_name',
                            'renderer'  => 'KS_KSDirectory_Block_Adminhtml_Directory_Grid_Column_Renderer_Village'
                        ));

                        $this->addColumn('postcode', array(
                            'header'    => Mage::helper('ksdirectory')->__('Post Code'),
                            'align'     =>'left',
                            'width'     => '50px',
                            'index'     => 'postcode'
                        ));
                    }
                }
            }
        }
        else{
            $showAction = false;
        }


        if($this->_isAllowedAction("edit") &&  $showAction){
            $this->addColumn('action',
                array(
                'header'    =>  Mage::helper('ksdirectory')->__('Action'),
                'type'      => 'action',
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
                'width'     => '170px',
                'renderer'  => 'KS_KSDirectory_Block_Adminhtml_Directory_Grid_Column_Renderer_Action'
            ));
        }

        return parent::_prepareColumns();
    }

    protected function _isAllowedAction($action) {
        return Mage::getSingleton('admin/session')->isAllowed("ksall/ksdirectory/actions/".$action);
    }
}