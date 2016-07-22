<?php
/**
 * @package ET_Filter
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Filter_Block_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute {

    public function __construct(){
        parent::__construct();

        if ($this->helper('filter')->isEnabled() && $this->helper('filter')->isMultipleChoiceFiltersEnabled()){
			$this->setTemplate('et/filter/catalog/layer/filter.phtml');
        }
    }
    
}