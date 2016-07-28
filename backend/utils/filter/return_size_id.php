<?php

include '../../../app/Mage.php';
Mage::init();
include '../../db_config.php';

$type =$_POST['type'];

// $resource = Mage::getSingleton('core/resource');
// $readConnection = $resource->getConnection('core_read');
// $table = $resource->getTableName('filter_attribute_url_key');
//$selected = 444;
// $query = 'SELECT attribute_code FROM ' . $table . ' WHERE option_id = '
//         . (int)$type . ' LIMIT 1';

//$sku = $readConnection->fetchOne($query);
//echo $sku;
//mage::log($sku);

$type_code = array();
$query = "SELECT * from sizechart WHERE (popular = '".$type."' OR uk = '".$type."' OR us = '".$type."' OR standard = '".$type."') ";
$result = mysql_query($query); 
mage::log($query);

//while($row = mysql_fetch_array($result)) {
//mage::log("foll:");
//mage::log($row);
//}
$numresult = mysql_numrows($result);

for($i = 0; $i<$numresult; $i++){
  $type_code["$i.popular"] = mysql_result($result,$i,'popular');
  $type_code["$i.uk"] = mysql_result($result,$i,'uk');
  $type_code["$i.us"] = mysql_result($result,$i,'us');
  $type_code["$i.standard"] = mysql_result($result,$i,'standard');
}
//mage::log("typecode:");
//mage::log($type_code);
echo json_encode($type_code);
   
?>