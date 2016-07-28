<?php

include '../../app/Mage.php';
Mage::init();

$type =$_POST['type'];

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('filter_attribute_url_key');
        //$selected = 444;
        $query = 'SELECT attribute_code FROM ' . $table . ' WHERE option_id = '
                 . (int)$type . ' LIMIT 1';

        $sku = $readConnection->fetchOne($query);
        echo $sku;
        mage::log($sku);


//echo $type;

?>