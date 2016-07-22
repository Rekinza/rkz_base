<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2014 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Pincode Column Grid 
 */
class Delhivery_Lastmile_Block_Adminhtml_Pincode_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('pincodeGrid');
        $this->setDefaultSort('pincode_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('lastmile/pincode')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('pincode_id', array(
            'header' => Mage::helper('lastmile')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'pincode_id',
        ));

        $this->addColumn('district', array(
            'header' => Mage::helper('lastmile')->__('District'),
            'index' => 'district',
        ));
        
        $this->addColumn('pin', array(
            'header' => Mage::helper('lastmile')->__('Pincode'),
            'width' => '100px',
            'index' => 'pin',
        ));

		$this->addColumn('pre_paid', array(
            'header' => Mage::helper('lastmile')->__('Pre Paid'),
            'width' => '100px',
            'align' => 'center',
            'index' => 'pre_paid',
            'type' => 'options',
            'options' => array(
                "Y" => 'Yes',
                "N" => 'No',
            ),
        ));
        $this->addColumn('pickup', array(
            'header' => Mage::helper('lastmile')->__('Pickup'),
            'width' => '100px',
            'align' => 'center',
            'index' => 'pickup',
            'type' => 'options',
            'options' => array(
                "Y" => 'Yes',
                "N" => 'No',
            ),
        ));
        $this->addColumn('cash', array(
            'header' => Mage::helper('lastmile')->__('Cash'),
            'width' => '100px',
            'align' => 'center',
            'index' => 'cash',
            'type' => 'options',
            'options' => array(
                "Y" => 'Yes',
                "N" => 'No',
            ),
        ));
        $this->addColumn('cod', array(
            'header' => Mage::helper('lastmile')->__('COD'),
            'width' => '100px',
            'align' => 'center',
            'index' => 'cod',
            'type' => 'options',
            'options' => array(
                "Y" => 'Yes',
                "N" => 'No',
            ),
        ));
	
        $this->addColumn('state_code', array(
            'header' => Mage::helper('lastmile')->__('State Code'),
            'width' => '150px',
            'align' => 'left',
            'index' => 'state_code',
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('lastmile')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        

        //$this->addExportType('*/*/exportCsv', Mage::helper('lastmile')->__('CSV'));
        //$this->addExportType('*/*/exportXml', Mage::helper('lastmile')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('pincode_id');
        $this->getMassactionBlock()->setFormFieldName('lastmile');

        /*$this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('lastmile')->__('Delete'),
            'url' => $this->getUrl('massDelete'),
            'confirm' => Mage::helper('lastmile')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('lastmile/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('lastmile')->__('Change status'),
            'url' => $this->getUrl('*//*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('lastmile')->__('Status'),
                    'values' => $statuses
                )
            )
        ));*/
        return $this;
    }

    
    

}