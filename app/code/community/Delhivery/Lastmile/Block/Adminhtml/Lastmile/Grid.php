<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2010-2011 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Waybills Column Grid  
 */
class Delhivery_Lastmile_Block_Adminhtml_Lastmile_Grid extends Delhivery_Lastmile_Block_Adminhtml_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('lastmileGrid');
        $this->setDefaultSort('lastmile_id');
        $this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);		
    }
	public function getGridUrl()
	{
	return $this->getUrl('*/*/grid', array('_current'=>true));
	}
    protected function _prepareCollection() {
        $collection = Mage::getModel('lastmile/lastmile')->getCollection();
        $this->setCollection($collection);
		$this->setDefaultFilter(array('state'=>1)); 
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('lastmile_id', array(
            'header' => Mage::helper('lastmile')->__('ID'),
            'align' => 'center',
            'width' => '30px',
            'index' => 'lastmile_id',
        ));

        $this->addColumn('awb', array(
            'header' => Mage::helper('lastmile')->__('AWB'),
            'index' => 'awb',
        ));
		$this->addColumn('shipment_to', array(
            'header' => Mage::helper('lastmile')->__('Ship to'),
            'index' => 'shipment_to',
        ));
        $this->addColumn('shipment_id', array(
            'header' => Mage::helper('lastmile')->__('Shipment#'),
            'index' => 'shipment_id',
        ));		
        $this->addColumn('state', array(
            'header' => Mage::helper('lastmile')->__('State'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'state',
            'type' => 'options',
            'options' => array(
                1 => 'Used',
                2 => 'UnUsed',
            ),
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('lastmile')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                "Assigned" => 'Assigned',
                "InTransit" => 'In Transit',
				"Dispatched" => 'Dispatched',
				"Pending" => 'Pending',
				"Delivered" => 'Delivered',
				"Returned" => 'Returned',
				"RTO" => 'RTO',
				"DL" => 'DL',
				"UD" => 'UD',
				"RT" => 'RT',
				"RTO" => 'RTO',
            ),
        ));		
        $this->addColumn('orderid', array(
            'header' => Mage::helper('lastmile')->__('Order#'),
            'renderer' => new Delhivery_Lastmile_Block_Adminhtml_Renderer_Orderid(),
        ));
		$this->addColumn('orderstatus', array(
            'header' => Mage::helper('lastmile')->__('Order Status'),
            'align' => 'left',
            'width' => '80px',
            'type' => 'options',
			'renderer' => new Delhivery_Lastmile_Block_Adminhtml_Renderer_Orderstatus(),
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('lastmile')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('lastmile')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('lastmile_id');
        $this->getMassactionBlock()->setFormFieldName('lastmile');

		/*$this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('lastmile')->__('Delete'),
            'url' => $this->getUrl('massDelete'),
            'confirm' => Mage::helper('lastmile')->__('Are you sure?')
        ));*/
        $this->getMassactionBlock()->addItem('manifest', array(
            'label' => Mage::helper('lastmile')->__('Submit Manifest'),
            'url' => $this->getUrl('*/*/manifest')
        ));	
		
		/* New Aaction for print Shipping Label added on 21-04-2014*/
		$this->getMassactionBlock()->addItem('shippinglabel', array(
            'label' => Mage::helper('lastmile')->__('Print Shipping Label'),
            'url' => $this->getUrl('*/*/shippinglabel')
        ));		

        /**$statuses = Mage::getSingleton('lastmile/status')->getOptionArray();

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
        ));
		*/
        return $this;
    }

   

}