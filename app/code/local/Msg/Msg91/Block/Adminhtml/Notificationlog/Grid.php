<?php

class Msg_Msg91_Block_Adminhtml_Notificationlog_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('log_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('msg91/notificationlog')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('msg91')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
        ));

        $this->addColumn('sent_date', array(
            'header' => Mage::helper('msg91')->__('Sent Date'),
            'index' => 'sent_date',
            'type' => 'datetime',
            'width' => '100px',
 'renderer' => 'msg91/adminhtml_notificationlog_renderer_date',
        ));
        $this->addColumn('sender_id', array(
            'header' => Mage::helper('msg91')->__('From'),
            'align' => 'left',
            'index' => 'sender_id',
            'filter_index' => 'main_table.sender_id',
        ));
        $this->addColumn('to', array(
            'header' => Mage::helper('msg91')->__('To'),
            'align' => 'left',
            'index' => 'to',
            'filter_index' => 'main_table.to',
        ));
        
        $this->addColumn('recipient', array(
            'header' => Mage::helper('msg91')->__('Recipient'),
            'align' => 'left',
            'index' => 'recipient',
        ));
        
        
        $this->addColumn('chars', array(
            'header' => Mage::helper('msg91')->__('Chars'),
            'align' => 'left',
            'index' => 'chars',
        ));
        
        $this->addColumn('length', array(
            'header' => Mage::helper('msg91')->__('Length'),
            'align' => 'left',
            'index' => 'length',
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('msg91')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Sent For MSG91',
                2 => 'Not Sent',
            ),
        ));
        $this->addColumn('scheduled_at', array(
            'header' => Mage::helper('msg91')->__('Scheduled Date'),
            'index' => 'scheduled_at',
            'type' => 'datetime',
            'width' => '100px',
       'renderer' => 'msg91/adminhtml_notificationlog_renderer_scheduledate',
        ));

 $this->addColumn('reason', array(
            'header' => Mage::helper('msg91')->__('Reason'),
            'align' => 'left',
            'index' => 'status',
	    'renderer' => 'msg91/adminhtml_notificationlog_renderer_reason',

        ));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('msg91');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('msg91')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('msg91')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('msg91/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('msg91')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('msg91')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
