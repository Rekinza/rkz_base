<?php
/**
 * @package ET_Filter
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Filter_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar{

    public function getPagerUrl($params = array()) {
        if (!$this->helper('filter')->isEnabled()) {
            return parent::getPagerUrl($params);
        }

        if ($this->helper('filter')->isCatalogSearch()) {
            $params['isLayerAjax'] = null;
            return parent::getPagerUrl($params);
        }

        return $this->helper('filter')->getPagerUrl($params);
    }

}