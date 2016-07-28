<?php
include 'db_config.php';
$qc_status = $_POST['qc_status'];
$inventory_data_status=$_POST['inventory_data_status'];

if ($qc_status =='accepted')
{
	$url = 'http://www.rekinza.com/backend/inventory/inventory_accepted_update.php';
	$fields = array(
							'qc_status' => urlencode($qc_status),
							'inventory_data_status' => urlencode($inventory_data_status),
					);

	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');

	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

	//execute post
	$result = curl_exec($ch);

	//close connection
	curl_close($ch); 	
}

else if ($qc_status =='rejected')
{
	$url = 'http://www.rekinza.com/backend/inventory/inventory_rejected_update.php';
	$fields = array(
							'qc_status' => urlencode($qc_status),
							'inventory_data_status' => urlencode($inventory_data_status),
					);

	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');

	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

	//execute post
	$result = curl_exec($ch);

	//close connection
	curl_close($ch); 	
}

else
{
	$url = 'http://www.rekinza.com/backend/inventory/inventory_maybe_update.php';
	$fields = array(
							'qc_status' => urlencode($qc_status),
							'inventory_data_status' => urlencode($inventory_data_status),
					);

	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');

	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

	//execute post
	$result = curl_exec($ch);

	//close connection
	curl_close($ch); 	
}
?>