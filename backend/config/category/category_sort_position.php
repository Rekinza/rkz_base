<?php

include 'db_config.php';
require_once '../../../app/Mage.php';
include_once("../../../login-with-google-using-php/config.php");
include_once("../../../login-with-google-using-php/includes/functions.php");
include '../../utils/access_block.php';

$email_logged_in = $_SESSION['google_data']['email'];
echo $email_logged_in;
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
		Mage::app();
			
		$category = Mage::getModel('catalog/category');
		$tree = $category->getTreeModel();
		$tree->load();
		$ids = $tree->getCollection()->getAllIds();


		?>


		<html>

			<head></head>
			<h1>Category Configuration</h1>
			<!--a href = "..\..\backend_home.php"><button class ="panel_button">Home</button></a-->
			<div id ="category_panels">
				<a href = "../../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
			
					<fieldset>
						<form action = "category_sort_update.php" method = "POST">
						<h3>Category/Sub Category List</h3>			
						<table id="report_table">
							
							<th>Name</th>
							<th></th>
							<?php
							if ($ids){
								foreach ($ids as $id){
									$cat = Mage::getModel('catalog/category');
									$cat->load($id);

									$entity_id = $cat->getId();
									$name = $cat->getName();
									$url_key = $cat->getUrlKey();
									$url_path = $cat->getUrlPath();
									?>
									<tr>
										<td name ='category_name'><?php echo $name?></td>
										<td name = 'category_id'><?php echo $entity_id?></td>
										<td><input type = "Submit" value = "Optimize Sorting"></td>
									</tr>
									<?php
									
								}
							}
							?>
							</table>
		<br>


					</fieldset>
					
				</div>	

		</html>
	<?php
	} //panelif ends
	else {
		echo "not allowed for this email id";
	}
} //strcmp ends
else {
	echo "Sorry! Not authorized.";
}
?>
<style>
body
{
	background-color: #F0F0F0  ;
}

h1 
{
	text-align: center;
	color: #bb1515;
	font-family: 'Arial';
	background-color: #b7c3c2;
	width:50%;
	margin-left:auto;
	margin-right:auto;
}

span
{
	float:left;
	display:inline-block;
	width:49%;
}

h2
{
	text-align: center;
	color: #bb1515;
	font-family: 'Arial';
	background-color: #b7c3c2;
	width:50%;
	margin-left:auto;
	margin-right:auto;
}


div
{
	margin-left:auto;
	margin-right:auto;
	width:50%;
	text-align:center;
	line-height:2em;
}

legend
{
	font-size: 1.2em;
	background-color :#669999;
	color : white;
	width:100%;

}

.panel_button
{
	color: white;
	background: #bb1515;
	border: 2px outset #d7b9c9;
	font-size:1.1em;
	border-radius:7px;
} 

a
{
	text-decoration:none;
}

a:link 
{
	color: aquamarine;
	
}

a:visited
{
	color:white;
}

a:hover
{
	color:grey;
}


</style>