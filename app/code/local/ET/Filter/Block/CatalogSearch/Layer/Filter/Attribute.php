<?php
/**
 * @package ET_Filter
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Filter_Block_CatalogSearch_Layer_Filter_Attribute extends ET_Filter_Block_Catalog_Layer_Filter_Attribute{

    public function __construct(){
        parent::__construct();
        $this->_filterModelName = 'catalogsearch/layer_filter_attribute';
    }

}
