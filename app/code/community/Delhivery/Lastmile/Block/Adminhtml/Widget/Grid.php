<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2010-2011 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Grid Widget for all Block 
 */
//require_once 'Mage/Adminhtml/Block/Widget/Grid.php';
class Delhivery_Lastmile_Block_Adminhtml_Widget_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function addColumn($columnId, $column) {
        if (is_array($column)) {
            $this->_columns[$columnId] = $this->getLayout()->createBlock('lastmile/adminhtml_widget_grid_column')
                            ->setData($column)
                            ->setGrid($this);
        }
        /* elseif ($column instanceof Varien_Object) {
          $this->_columns[$columnId] = $column;
          } */ else {
            throw new Exception(Mage::helper('adminhtml')->__('Wrong column format'));
        }

        $this->_columns[$columnId]->setId($columnId);
        $this->_lastColumnId = $columnId;
        return $this;
    }

}
