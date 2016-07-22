<?php

	include 'db_config.php';
	
	echo "running<br>";
	
	$query = "SELECT id,email from mailer_lkt_2 WHERE id BETWEEN 112338 AND 400000";
	
	$result = mysql_query($query);
		
	$numresult = mysql_numrows($result);

	$i = 0;
	
	while ( $i < $numresult )
	{
		$email = mysql_result($result,$i,'email'); 
		
		$table_id = mysql_result($result,$i,'id');
		
		
		
		$url = 'http://picasaweb.google.com/data/entry/api/user/'.$email;  
			
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents

		$data = curl_exec($ch); // execute curl request
		curl_close($ch);

		$response = simplexml_load_string($data);
		
		
		$id = $response->id;
		
		$id = substr($id, strrpos($id, 'api/user/') + 9);
		
		if($id)
		{	
			$url = "https://www.googleapis.com/plus/v1/people/";
			
			$url= $url.$id."?key=AIzaSyDnuGUgImLaWKfDKfeDLtKoynYpinjAsDo";
			
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents

			$json_response = curl_exec($ch); // execute curl request

			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$err     = curl_errno( $ch );
			$errmsg  = curl_error( $ch );
			$header  = curl_getinfo( $ch );
			
			curl_close($ch);

			$response = json_decode($json_response, true);

			$gender = $response['gender'];
			
			$query = "UPDATE mailer_lkt_2 SET gender = '".$gender."' WHERE id = '".$table_id."'";
			
	
			$result2 = mysql_query($query);
			
			echo mysql_error();
			
		}
		$i++;
	}
	echo "Done!";
/*include '../app/Mage.php';

Mage::init();

$category = Mage::getModel('catalog/category')->getCollection()
                    ->addAttributeToFilter('level',2);

$catIds = $category->getAllIds();

$cats = array();
$i=0;

if ($catIds){
    foreach ($catIds as $id){
        $j=0;
        $cat = Mage::getModel('catalog/category');
        $cat->load($id);
        $cats[$i]["Category ID:"] = $id;
        $cats[$i]["Category Name:"] = $cat->getName();
        $subcats = $cat->getChildren();
        foreach(explode(',',$subcats) as $subCatid){
            $subcat = Mage::getModel('catalog/category');
            $subcat->load($subCatid);
            $cats[$i][$j]["Subcategory ID:"] = $subCatid;
            $cats[$i][$j]["Subcategory Name:"] = $subcat->getName();
            $j++;
        }
        $i++;
    }
} 

foreach($cats as $row){
    echo "<pre>";
    print_r($row);
    echo "</pre>";
}
*/


?>