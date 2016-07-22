<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2010-2011 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Grid Column Renderer 
 */
//require_once 'Mage/Adminhtml/Block/Widget/Grid/Column.php';

class Delhivery_Lastmile_Block_Adminhtml_Widget_Grid_Column extends Mage_Adminhtml_Block_Widget_Grid_Column {

    protected function _getRendererByType() {
        switch (strtolower($this->getType())) {
            case 'lastmile':
                $rendererClass = 'lastmile/adminhtml_widget_grid_column_renderer_lastmile';
                break;
            default:
                $rendererClass = parent::_getRendererByType();
                break;
        }
        return $rendererClass;
    }

    protected function _getFilterByType() {
        switch (strtolower($this->getType())) {
            case 'lastmile':
                $filterClass = 'lastmile/adminhtml_widget_grid_column_filter_lastmile';
                break;
            default:
                $filterClass = parent::_getFilterByType();
                break;
        }
        return $filterClass;
    }

}