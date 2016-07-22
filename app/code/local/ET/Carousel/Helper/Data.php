<?php
/**
 * @package ET_Carousel
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Carousel_Helper_Data extends Mage_Catalog_Helper_Output {
    
    public function isNew($product=null) {
        if (! $product instanceof Mage_Catalog_Model_Product ){
            return false;
        }
        if (! $product->getId() ){
            return false;
        }
        if (!$product->getNewsFromDate() && !$product->getNewsToDate()) {
            return false;
        }

        $today_start = Mage::app()->getLocale()->date()
        ->setTime('00:00:00')
        ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $today_end  = Mage::app()->getLocale()->date()
        ->setTime('23:59:59')
        ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        if ($nfd = $product->getNewsFromDate()) {
            if ($nfd > $today_end) {
                return false;
            }
        }
         
        if ($ntd = $product->getNewsToDate()) {
            if ($ntd < $today_start) {
                return false;
            }
        }

        return true;
    }

    public function isSpecial($product = null){
        if (! $product instanceof Mage_Catalog_Model_Product ){
            return false;
        }
        if (! $product->getId() ){
            return false;
        }
        if (!$product->getSpecialFromDate() && !$product->getSpecialToDate()) {
            return false;
        }

        $today_start = Mage::app()->getLocale()->date()
        ->setTime('00:00:00')
        ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $today_end  = Mage::app()->getLocale()->date()
        ->setTime('23:59:59')
        ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);


        if ($sfd = $product->getSpecialFromDate()) {
            if ($sfd > $today_end) {
                return false;
            }
        }
         
        if ($std = $product->getSpecialToDate()) {
            if ($std < $today_start) {
                return false;
            }
        }

        return true;
    }


    public function isFeatured($product=null, $att='is_featured'){
        if (! $product instanceof Mage_Catalog_Model_Product){
            return false;
        }
        if (! $product->getId()){
            return false;
        }
        if (! $product->hasData('is_featured')){
            return false;
        }
        $is_featured = $product->getData('is_featured');
        if (empty($is_featured) || !$is_featured || strtolower($is_featured)=='false') {
            return false;
        }

        return true;
    }

    /**
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @param int $type
     * @return ET_Products_Helper_Data $this
     */
    public function isNewFilter(&$collection, $type = 0){
        switch ($type) {
            default:
            case 0:

                break;
            case 1:

                $today_start = Mage::app()->getLocale()->date()
                ->setTime('00:00:00')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

                $today_end  = Mage::app()->getLocale()->date()
                ->setTime('23:59:59')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

                $collection
                ->addAttributeToFilter('news_from_date', array(
                    array('date' => true, 'to' => $today_end),
                    array('is' => new Zend_Db_Expr('null'))
                ), 'left')
                ->addAttributeToFilter('news_to_date', array(
                    array('date' => true, 'from' => $today_start),
                    array('is' => new Zend_Db_Expr('null'))
                ), 'left')
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'news_from_date', 'is' => new Zend_Db_Expr('not null')),
                        array('attribute' => 'news_to_date',   'is' => new Zend_Db_Expr('not null'))
                    )
                );

                break;
            case 2:

                $yesterday_end = Mage::app()->getLocale()->date()
                ->setTime('23:59:59')
                ->subDayOfYear(1)
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                $tomorow_start = Mage::app()->getLocale()->date()
                ->setTime('00:00:00')
                ->addDayOfYear(1)
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

                $collection
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'news_from_date',  array(
                            array('date' => true, 'from' => $tomorow_start),
                            array('is' => new Zend_Db_Expr('null'))
                        )),
                        array('attribute' => 'news_to_date', array(
                            array('date' => true, 'to' => $yesterday_end),
                            array('is' => new Zend_Db_Expr('null'))
                        ))
                    )
                )
                ;
                break;
        }
        return $this;
    }

    /**
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @param int $type
     * @return ET_Products_Helper_Data $this
     */
    public function isSpecialFilter(&$collection, $type = 0){
        switch ($type) {
            default:
            case 0:

                break;
            case 1:

                $today_start  = Mage::app()->getLocale()->date()
                ->setTime('00:00:00')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

                $today_end  = Mage::app()->getLocale()->date()
                ->setTime('23:59:59')
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

                $collection
                ->addAttributeToFilter('special_from_date', array('or' => array(
                    array('date' => true, 'to' => $today_end),
                    array('is' => new Zend_Db_Expr('null')))
                ), 'left')
                ->addAttributeToFilter('special_to_date', array('or' => array(
                    array('date' => true, 'from' => $today_start),
                    array('is' => new Zend_Db_Expr('null')))
                ), 'left')
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'special_from_date', 'is' => new Zend_Db_Expr('not null')),
                        array('attribute' => 'special_to_date', 'is' => new Zend_Db_Expr('not null'))
                    )
                );

                break;
            case 2:

                $yesterday_end = Mage::app()->getLocale()->date()
                ->setTime('23:59:59')
                ->subDayOfYear(1)
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                $tomorow_start = Mage::app()->getLocale()->date()
                ->setTime('00:00:00')
                ->addDayOfYear(1)
                ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

                $collection
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'special_from_date',  array(
                            array('date' => true, 'from' => $tomorow_start),
                            array('is' => new Zend_Db_Expr('null'))
                        )),
                        array('attribute' => 'special_to_date', array(
                            array('date' => true, 'to' => $yesterday_end),
                            array('is' => new Zend_Db_Expr('null'))
                        ))
                    )
                )
                ;
                break;
        }
        return $this;
    }

    /**
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @param int $type
     * @return ET_Products_Helper_Data $this
     */
    public function isFeaturedFilter(&$collection, $type=0){
        // required is_featured attribute
        $is_featured_attribute = Mage::getModel('catalog/resource_eav_attribute')
        ->loadByCode('catalog_product', 'is_featured');
        if ( !$is_featured_attribute->getId() ){
            return 0;
        } else {
            switch ($type) {
                default:
                case 0:
                    break;
                case 1:
                    $collection->addAttributeToSelect('is_featured');
                    $collection->addAttributeToFilter('is_featured', array('eq' => 1));
                    break;
                case 2:
                    $collection->addAttributeToSelect('is_featured');
                    $collection->addAttributeToFilter('is_featured', array(
                        array('null'=>1),
                        array('eq' => 0)
                    ), 'left');
                    break;
            }
        }
    }


    /**
     * Parse and build target attribute for links.
     * @param string $value (_self, _blank, _windowopen, _modal)
     * _blank     Opens the linked document in a new window or tab
     * _self     Opens the linked document in the same frame as it was clicked (this is default)
     * _parent     Opens the linked document in the parent frame
     * _top     Opens the linked document in the full body of the window
     * _windowopen  Opens the linked document in a Window
     * _modal        Opens the linked document in a Modal Window
     */
    public  function parseTarget ($type='_self'){
        $target = '';
        switch($type){
            default:
            case '_self':
                break;
            case '_blank':
            case '_parent':
            case '_top':
                $target = 'target="'.$type.'"';
                break;
            case '_windowopen':
                $target = "onclick=\"window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,false');return false;\"";
                break;
            case '_modal':
                // user process
                break;
        }
        return $target;
    }

    /**
     * Truncate string by $length
     * @param string $string
     * @param int $length
     * @param string $etc
     * @return string
     */
    public  function truncate ($string, $length, $etc='...') {
        return defined('MB_OVERLOAD_STRING')
        ? $this->_mb_truncate($string, $length, $etc)
        : $this->_truncate($string, $length, $etc);
    }

    /**
     * Truncate string if it's size over $length
     * @param string $string
     * @param int $length
     * @param string $etc
     * @return string
     */
    private  function _truncate ($string, $length, $etc='...')
    {
        if ($length>0 && $length<strlen($string)){
            $buffer = '';
            $buffer_length = 0;
            $parts = preg_split('/(<[^>]*>)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
            $self_closing_tag = split(',', 'area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed');
            $open = array();

            foreach($parts as $i => $s){
                if( false===strpos($s, '<') ){
                    $s_length = strlen($s);
                    if ($buffer_length + $s_length < $length){
                        $buffer .= $s;
                        $buffer_length += $s_length;
                    } else if ($buffer_length + $s_length == $length) {
                        if ( !empty($etc) ){
                            $buffer .= ($s[$s_length - 1]==' ') ? $etc : " $etc";
                        }
                        break;
                    } else {
                        $words = preg_split('/([^\s]*)/', $s, - 1, PREG_SPLIT_DELIM_CAPTURE);
                        $space_end = false;
                        foreach ($words as $w){
                            if ($w_length = strlen($w)){
                                if ($buffer_length + $w_length < $length){
                                    $buffer .= $w;
                                    $buffer_length += $w_length;
                                    $space_end = (trim($w) == '');
                                } else {
                                    if ( !empty($etc) ){
                                        $more = $space_end ? $etc : " $etc";
                                        $buffer .= $more;
                                        $buffer_length += strlen($more);
                                    }
                                    break;
                                }
                            }
                        }
                        break;
                    }
                } else {
                    preg_match('/^<([\/]?\s?)([a-zA-Z0-9]+)\s?[^>]*>$/', $s, $m);
                    //$tagclose = isset($m[1]) && trim($m[1])=='/';
                    if (empty($m[1]) && isset($m[2]) && !in_array($m[2], $self_closing_tag)){
                        array_push($open, $m[2]);
                    } else if (trim($m[1])=='/') {
                        $tag = array_pop($open);
                        if ($tag != $m[2]){
                            // uncomment to to check invalid html string.
                            // die('invalid close tag: '. $s);
                        }
                    }
                    $buffer .= $s;
                }
            }
            // close tag openned.
            while(count($open)>0){
                $tag = array_pop($open);
                $buffer .= "</$tag>";
            }
            return $buffer;
        }
        return $string;
    }

    /**
     * Truncate mutibyte string if it's size over $length
     * @param string $string
     * @param int $length
     * @param string $etc
     * @return string
     */
    private  function _mb_truncate ($string, $length, $etc='...')
    {
        $encoding = mb_detect_encoding($string);
        if ($length>0 && $length<mb_strlen($string, $encoding)){
            $buffer = '';
            $buffer_length = 0;
            $parts = preg_split('/(<[^>]*>)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
            $self_closing_tag = explode(',', 'area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed');
            $open = array();

            foreach($parts as $i => $s){
                if (false === mb_strpos($s, '<')){
                    $s_length = mb_strlen($s, $encoding);
                    if ($buffer_length + $s_length < $length){
                        $buffer .= $s;
                        $buffer_length += $s_length;
                    } else if ($buffer_length + $s_length == $length) {
                        if ( !empty($etc) ){
                            $buffer .= ($s[$s_length - 1]==' ') ? $etc : " $etc";
                        }
                        break;
                    } else {
                        $words = preg_split('/([^\s]*)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
                        $space_end = false;
                        foreach ($words as $w){
                            if ($w_length = mb_strlen($w, $encoding)){
                                if ($buffer_length + $w_length < $length){
                                    $buffer .= $w;
                                    $buffer_length += $w_length;
                                    $space_end = (trim($w) == '');
                                } else {
                                    if ( !empty($etc) ){
                                        $more = $space_end ? $etc : " $etc";
                                        $buffer .= $more;
                                        $buffer_length += mb_strlen($more);
                                    }
                                    break;
                                }
                            }
                        }
                        break;
                    }
                } else {
                    preg_match('/^<([\/]?\s?)([a-zA-Z0-9]+)\s?[^>]*>$/', $s, $m);
                    //$tagclose = isset($m[1]) && trim($m[1])=='/';
                    if (empty($m[1]) && isset($m[2]) && !in_array($m[2], $self_closing_tag)){
                        array_push($open, $m[2]);
                    } else if (trim($m[1])=='/') {
                        $tag = array_pop($open);
                        if ($tag != $m[2]){
                            // uncomment to to check invalid html string.
                            // die('invalid close tag: '. $s);
                        }
                    }
                    $buffer .= $s;
                }
            }
            // close tag openned.
            while(count($open)>0){
                $tag = array_pop($open);
                $buffer .= "</$tag>";
            }
            return $buffer;
        }
        return $string;
    }

    public function createAttribute ($code, $label, $attribute_type, $product_type = null) {
        $_attribute_data = array(
            'attribute_code' => $code,
            'is_global' => '1',
            'frontend_input' => $attribute_type, //'boolean',
            'default_value_text' => '',
            'default_value_yesno' => '0',
            'default_value_date' => '',
            'default_value_textarea' => '',
            'is_unique' => '0',
            'is_required' => '0',
            'apply_to' => is_array($product_type) ? $product_type : array($product_type), //array('grouped')
            'is_configurable' => '0',
            'is_searchable' => '0',
            'is_visible_in_advanced_search' => '0',
            'is_comparable' => '0',
            'is_used_for_price_rules' => '0',
            'is_wysiwyg_enabled' => '0',
            'is_html_allowed_on_front' => '1',
            'is_visible_on_front' => '0',
            'used_in_product_listing' => '0',
            'used_for_sort_by' => '0',
            'frontend_label' => array($label)
        );


        /* @var $model Mage_Catalog_Model_Entity_Attribute */
        $model = Mage::getModel('catalog/resource_eav_attribute');

        /* @var $helper Mage_Catalog_Helper_Product */
        $helper = Mage::helper('catalog/product');

        //validate attribute_code
        if (isset($_attribute_data['attribute_code'])) {
            $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{1,254}$/'));
            if ( !$validatorAttrCode->isValid($_attribute_data['attribute_code']) ) {
                die(
                    Mage::helper('catalog')->__('Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.')
                );
            }
        }

        //validate frontend_input
        if (isset($_attribute_data['frontend_input'])) {
            /** @var $validatorInputType Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator */
            $validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
            if ( !$validatorInputType->isValid($_attribute_data['frontend_input']) ) {
                $messages = array();
                foreach ($validatorInputType->getMessages() as $message) {
                    $messages[] = $message;
                }
                die(
                    implode('<br>', $messages)
                );
            }
        }

        /**
         * @todo add to helper and specify all relations for properties
         */
        $_attribute_data['source_model'] = $helper->getAttributeSourceModelByInputType($_attribute_data['frontend_input']);
        $_attribute_data['backend_model'] = $helper->getAttributeBackendModelByInputType($_attribute_data['frontend_input']);

        if (!isset($_attribute_data['is_configurable'])) {
            $_attribute_data['is_configurable'] = 0;
        }
        if (!isset($_attribute_data['is_filterable'])) {
            $_attribute_data['is_filterable'] = 0;
        }
        if (!isset($_attribute_data['is_filterable_in_search'])) {
            $_attribute_data['is_filterable_in_search'] = 0;
        }

        if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
            $_attribute_data['backend_type'] = $model->getBackendTypeByInput($_attribute_data['frontend_input']);
        }

        $defaultValueField = $model->getDefaultValueByInput($_attribute_data['frontend_input']);
        if ($defaultValueField && array_key_exists($defaultValueField, $_attribute_data)) {
            $_attribute_data['default_value'] = $_attribute_data[$defaultValueField];
        }

        /** @var $helperCatalog Mage_Catalog_Helper_Data */
        $helperCatalog = Mage::helper('catalog');
        //labels
        foreach ($_attribute_data['frontend_label'] as & $value) {
            if ($value) {
                $value = $helperCatalog->stripTags($value);
            }
        }
        if (!empty($_attribute_data['option']) && !empty($_attribute_data['option']['value']) && is_array($_attribute_data['option']['value'])) {
            foreach ($_attribute_data['option']['value'] as $key => $values) {
                $_attribute_data['option']['value'][$key] = array_map(array($helperCatalog, 'stripTags'), $values);
            }
        }

        $model->addData($_attribute_data);
        $model->setEntityTypeId(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId());
        $model->setIsUserDefined(1);

        try {
            $model->save();
        } catch (Exception $e) {
            die('<p>Sorry, error occured while trying to save the attribute. Error: '.$e->getMessage().'</p>');
        }

        return true;
    }

    public function insertAttributeToGroup ( $code, $group_name = 'General'){

        $installer = Mage::getModel('core/resource_setup');
        $entity_type_id = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();

        $resource = Mage::getSingleton('core/resource');
        $read  = $resource->getConnection('core_read');
        $write = $resource->getConnection('core_write');

        //get attribute_set_id
        $select = $read->select()
        ->from($installer->getTable("eav_attribute_set"), array('attribute_set_id'))
        ->where("entity_type_id=?", $entity_type_id);

        $attribute_sets = $read->fetchAll($select);

        foreach($attribute_sets as $attribute_set) {
            $attribute_set_id = $attribute_set['attribute_set_id'];

            $select = $read->select()
            ->from($installer ->getTable("eav_attribute"), array('attribute_id'))
            ->where("entity_type_id=?", $entity_type_id)
            ->where("attribute_code=?", $code);

            $attribute = $read->fetchRow($select);
            $attribute_id = $attribute['attribute_id'];

            $select = $read->select()
            ->from($installer->getTable("eav_attribute_group"), array('attribute_group_id'))
            ->where("attribute_set_id=?", $attribute_set_id)
            ->where("attribute_group_name=?", $group_name);

            $attribute_group = $read->fetchRow($select);
            $attribute_group_id = $attribute_group['attribute_group_id'];

            $write->beginTransaction();
            $write->insert(
                $installer->getTable("eav_entity_attribute"),
                array(
                    "entity_type_id" => $entity_type_id,
                    "attribute_set_id" => $attribute_set_id,
                    "attribute_group_id" => $attribute_group_id,
                    "attribute_id" => $attribute_id,
                    "sort_order" => 255
                )
            );
            $write->commit();
        }

        return true;
    }

    public function removeAttributeByCode ( $code ){
        //get entity_type_id
        $entity_type = Mage::getSingleton("eav/entity_type")->loadByCode("catalog_product");
        $entity_type_id = $entity_type->getId();

        $collection = Mage::getModel("eav/entity_attribute")
        ->getCollection()
        ->addFieldToFilter("entity_type_id", $entity_type_id)
        ->addFieldToFilter("attribute_code", $code)
        ->getFirstItem()->delete();
    }

}
