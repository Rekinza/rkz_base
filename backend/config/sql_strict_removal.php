<?php
include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';
include 'db_config.php';

$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if ($numresult > 0)
{
$email = mysql_result($result,0,'email');
$access_level = mysql_result($result,0,'access_level');

	$blockname = "control panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{
	?>
	<html>
	<body>
		<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
		
	</body>
	</html>
	<?php	

	$query1 = "SELECT @@GLOBAL.sql_mode";
	$query2 = "SELECT @@SESSION.sql_mode";

	$result1 = mysql_query($query1);
	$sql_mode1 = mysql_result($result1, 0);

	$result2 = mysql_query($query2);
	$sql_mode2 = mysql_result($result2, 0);

	$findme = "STRICT_TRANS_TABLES";

	$pos1 = strpos($sql_mode1, $findme);
	$pos2 = strpos($sql_mode2, $findme);

	echo $sql_mode1."<br>";

	echo $sql_mode2."<br>";


	echo $pos1."<br>";
	echo $pos2."<br>";

	if($pos1 === false && $pos2 === false){
		echo "No Fix Required. Stop Wasting Time";
	}
	else
	{
		$query3 = "SET @@global.sql_mode = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'";
		$result3 = mysql_query($query3);
		if ($result3 == 'TRUE')
		{
			echo 'Panel Fixed. Go Work';
		}
		else
		{
			echo 'Panel Fix Failed. Take a Break. Contact Rishi/Stuti';
		}

	}

	mysql_close();
	} //panelif ends
	else {
		echo "not allowed for this email id";
	}
} //numrows ends
else {
	echo "Sorry! Not authorized.";
}
?>


