<?php

include '../db_config.php';

require_once('../../app/Mage.php');
Mage::app();

$query = "SELECT DISTINCT(o.customer_email) FROM sales_flat_order o";

$result = mysql_query($query);

$numrows = mysql_numrows($result);

/* 
$min_diff = '480';   //WRT 28 June
$from_date = date("Y-m-d H:i:s", strtotime("-".$min_diff." day"));


$min_diff = '380'; 

$to_date = date("Y-m-d H:i:s", strtotime("-".$min_diff." day"));

echo $from_date."<br>";
echo $to_date."<br>";
$to_date = date("Y-m-d H:i:s");
$collection->addAttributeToFilter('created_at', array(
    'from' => $from_date,
    'to' => $to_date
    ));

 */
//foreach ($collection as $col) 
$i = 0;
while ($i <$numrows)
{
	$email = mysql_result($result,$i,'customer_email');     //$col->getCustomerEmail();
	echo $email."<br>";
	
	$pos = strpos($email,'gmail');

	if ($pos === false) 
	{
		//do nothing
	}
	else
	{
		fetch_customer_detail($email);
		
	}
	$i++;
}

echo "Done!!";

function fetch_customer_detail($email)
{

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
			
			var_dump($response);
			
			if($response['organizations'])
			{
				$user_data = "";
				//var_dump($response['organizations']);
				
				for($i = 0; $i < count($response['organizations']); $i++)
				{
					$CT_IDS[] = implode(",",$response['organizations'][$i]);
				}
				$temp = implode(',',$CT_IDS);
				$user_data = $user_data." ".$temp;
				
				//echo $user_data." Here<br>";
				
			}
			else if($response['urls'])
			{
				$user_data = "";
				
				for($i = 0; $i < count($response['urls']); $i++)
				{
					$CT_IDS[] = implode(",",$response['urls'][$i]);
				}
				$temp = implode(',',$CT_IDS);
				$user_data = $user_data." ".$temp;
				
			}
			
			if($user_data)
			{			
				$user_data = mysql_real_escape_string($user_data);
				$query = "INSERT INTO buyer_profiles VALUES ('','$user_data')";
				
				$result2 = mysql_query($query);
				
				echo mysql_error();
			}
	
			
		}
}



?>