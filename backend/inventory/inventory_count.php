<?php


include 'db_config.php';


$creation_start_date = '2016-06-01 00:00:00';
$creation_end_date = '2016-06-29 00:00:00';

$i = 0;

while ($i <60)
{
	/* Format our dates */
	$creation_start_date = date('Y-m-d H:i:s', strtotime($creation_start_date));
	$creation_end_date = date('Y-m-d H:i:s', strtotime($creation_end_date));

	/* Set end date time till 23 hrs, 59 min and 59 seconds i.e. EOD */
	$creation_end_date = strtotime ( '+23 hours' , strtotime ($creation_end_date )) ;
	$creation_end_date = date('Y-m-d H:i:s', $creation_end_date);


	$creation_end_date = strtotime ( '+59 minutes' , strtotime ($creation_end_date ));
	$creation_end_date = date('Y-m-d H:i:s', $creation_end_date);

	$creation_end_date = strtotime ( '+59 seconds' , strtotime ($creation_end_date ));
	$creation_end_date = date('Y-m-d H:i:s', $creation_end_date);


	echo $creation_start_date."<br>";
	echo $creation_end_date."<br><br>";

		$query_grouped = "SELECT qc_owner, SUM(quantity) as total FROM inventory WHERE creation_date BETWEEN '$creation_start_date' AND '$creation_end_date' GROUP BY qc_owner";


	$result_group_wise = mysql_query($query_grouped);

	$numresult_grouped = mysql_numrows($result_group_wise);


	if($numresult_grouped >0)
	{
		?>
		<table>
		<th>QC Owner</th>
		<th>Total Processed</th>
		<th>Details</th>
		
		<?php
		$j = 0;
		while ( $j < $numresult_grouped )
		{
			$qc_owner = mysql_result($result_group_wise,$j,'qc_owner');
			$qc_count = mysql_result($result_group_wise,$j,'total');
			
			?>
			<tr>
				<td><?php echo $qc_owner?></td>
				<td><?php echo $qc_count?></td>
			
			<?php
			$query_at_qc_owner_level = "SELECT qc_status as status,SUM(quantity) as qc_status_total from inventory where creation_date BETWEEN '$creation_start_date' AND '$creation_end_date' AND qc_owner = '".$qc_owner."' GROUP by qc_status";

			$result_at_qc_owner_level = mysql_query($query_at_qc_owner_level);

			$numresult_at_qc_owner_level = mysql_numrows($result_at_qc_owner_level);
			
					
			$k = 0;
			
			?>
			<td style = "text-align:left">
			<?php
			
			while ($k < $numresult_at_qc_owner_level)
			{
				$status = mysql_result($result_at_qc_owner_level,$k,'status');
				$qc_status_count = mysql_result($result_at_qc_owner_level,$k,'qc_status_total');
				?>
					<b><?php echo $status." " ?></b><?php echo $qc_status_count?>
				
				<?php
				$k++;
			} 
			?>
			</td>
			</tr>
			<?php
			
	/* 		 	while ($row = mysql_fetch_array($result_at_qc_owner_level, MYSQL_ASSOC)) {
			print_r($row);
		}  */	

			echo "<br>";
			
			$j++;
		}
		?>
		
		</table>
		<?php
	 	
	}
$i++;


	// /* Format our dates */
	// $creation_start_date = date('Y-m-d H:i:s', strtotime($creation_start_date));
	// $creation_end_date = date('Y-m-d H:i:s', strtotime($creation_end_date));


	// echo $creation_start_date."<br>";
	// echo $creation_end_date."Hu<br><br>";

	/* Set end date time till 23 hrs, 59 min and 59 seconds i.e. EOD */
	$creation_start_date = strtotime ( '+24 hours' , strtotime ($creation_start_date)) ;
	$creation_start_date = date('Y-m-d H:i:s', $creation_start_date);

	$creation_end_date = $creation_start_date;
	
}


mysql_close();
?>
<style>

body
{
	background-color: #F9FFFB;
}

#seller_pickup_id
{
	width:1%;
	text-align:center;
	
}

h1
{
	background-color: #E3E0FA;
	text-align:center;
	width:40%;
	margin-left:auto;
	margin-right:auto;
	margin-bottom:2em;
	font-family: 'Century Schoolbook';
		

}

table
	{
		margin-left: auto;
		margin-right: auto;
		font-color: #d3d3d3;
		background-color: #ADD8E6;
	
	}
	
th
	{
		background-color: #C0C0C0;
		font-family: 'Georgia';
		font-size:1.2em;
	}
	
td
	{
		text-align : center;
	}
	
	
tr:nth-child(odd)
	{
			background-color: #EAF1FB;
			font-size:1.1em;
	}

tr:nth-child(even)
	{
			background-color: #CEDEF4;
			font-size:1.1em;
	}

tr.highlight   
	{    
		background-color: #063774;   
		color: White;   
	}  

textarea
	{
		height:120px;
	}
	
#btnExport
	{
		display:block;
		margin-left: auto;
		margin-right: auto;
		margin-bottom:2em;
		height: 35px;
		color: white;
		border-radius: 10px;
		text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
		background: rgb(202, 60, 60); /* this is maroon */
	}	

input
{
		width:80%;
	
	
}

input#labels_button
{
	cursor:pointer; /*forces the cursor to change to a hand when the button is hovered*/
	padding:5px 25px; /*add some padding to the inside of the button*/
	background:rgb(202, 60, 60);; /*the colour of the button*/
	border:1px solid #33842a; /*required or the default border for the browser will appear*/
	/*give the button curved corners, alter the size as required*/
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	border-radius: 10px;
	/*give the button a drop shadow*/
	-webkit-box-shadow: 0 0 4px rgba(0,0,0, .75);
	-moz-box-shadow: 0 0 4px rgba(0,0,0, .75);
	box-shadow: 0 0 4px rgba(0,0,0, .75);
	/*style the text*/
	color:#f3f3f3;
	font-size: 14 px;
	margin : auto;
	display:block;
	width:8%;
}

input#labels_button:hover, input#gobutton:focus
{
	background-color :#399630; /*make the background a little darker*/
	/*reduce the drop shadow size to give a pushed button effect*/
	-webkit-box-shadow: 0 0 1px rgba(0,0,0, .75);
	-moz-box-shadow: 0 0 1px rgba(0,0,0, .75);
	box-shadow: 0 0 1px rgba(0,0,0, .75);
}

.ui-autocomplete {
    position: absolute;
    z-index: 1000;
    cursor: default;
    padding: 0;
    margin-top: 2px;
    list-style: none;
    background-color: #ffffff;
    border: 1px solid #ccc
    -webkit-border-radius: 5px;
       -moz-border-radius: 5px;
            border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
       -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
}
.ui-autocomplete > li {
  padding: 3px 20px;
}
.ui-autocomplete > li.ui-state-focus {
  background-color: #DDD;
}
.ui-helper-hidden-accessible {
  display: none;
}

</style>

<script>

/*************Fill upload status using autofill******************/
$(function() {
    
    //autocomplete
    $(".auto").autocomplete({
        source: "inventory_autofill_upload_status.php",
        minLength: 1
    });                

});

function fnExcelReport()
{
	var tab_text="<table><tr>";
    var textRange;
    tab = document.getElementById('report_table'); // id of actual table on your page

	console.log(tab.rows.length);
    for(j = 0 ; j < tab.rows.length ; j++) 
    {   
        tab_text=tab_text+tab.rows[j].innerHTML;
        tab_text=tab_text+"</tr><tr>";
    }

    tab_text = tab_text+"</tr></table>";

	var txt = new Blob([tab_text], {type: "text/plain;charset=utf-8"});
	saveAs(txt,"Inventory_Accepted_Report.xls");
}

/********************* Validate numeric entry in form field*********************/
function validate(evt) 
{
	var theEvent = evt || window.event;
	var key = theEvent.keyCode || theEvent.which;
	key = String.fromCharCode( key );
	var regex = /[0-9]|\./;
	if( !regex.test(key) ) 
	{
		theEvent.returnValue = false;
		if(theEvent.preventDefault) theEvent.preventDefault();
	}
}

/********************* Suggested price autofill based on retail value*********************/
function FillSuggPrice(value) 
{
	/*
    var retail_price = document.getElementById('retail_value');
	console.log(value);
    document.getElementById('suggested_price').value=0.3 * retail_price.value;
      */  
}


</script>