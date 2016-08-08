<?php

umask(0);

require_once 'OrderGenerator.php';
require_once 'CustomerGenerator.php';
require_once '../../app/Mage.php';
include '../db_config.php';
include '../utils/access_block.php';

Mage::app();

?>

<html>
        <head>
            <!--script src="jquery-1.11.1.js"></script-->
			<!--script type="text/javascript" src="js/jquery/jquery-1.7.1.min.js"></script-->
			<script type="text/javascript" src="../jsPDF/examples/js/jquery/jquery-ui-1.8.17.custom.min.js"></script>
			<script type="text/javascript" src="../jsPDF/dist/jspdf.debug.js"></script>
			<script type="text/javascript" src="../jsPDF/examples/js/basic.js"></script>

            <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
            <script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>
            <script type="text/javascript">
			
            window.grandtotal = 0;
            function fetchdetails()
            {
                var table = document.getElementById("items_table");
                var rowCount = table.rows.length;
                var skuid = "sku"+(rowCount-1);
                var nameid = "name"+(rowCount-1);
                var imageid = "image"+(rowCount-1);
                var priceid = "price"+(rowCount-1);
                var taxid = "tax"+(rowCount-1);
                var flagid = "flag"+(rowCount-1);
                var subtotalid = "subtotal"+(rowCount-1);
                //console.log("skuid:"+skuid);
               $.ajax({
                type:'POST',
                url:'return_product_from_sku.php',
                data:
                {
                    'sku':$('[id="'+skuid+'"]').val()
                },
            
                success:function(message)
                {
                    if(message){
                    //console.log(message);
                    var json_data = JSON.parse(message);
                    //console.log(json_data);
                    $('[id="'+nameid+'"]').val(json_data.productname);
                    $('[id="'+imageid+'"]').html('<img src="' + json_data.image + '" style = "width:100px" />');
                    $('[id="'+priceid+'"]').val(json_data.price);
                    $('[id="'+taxid+'"]').val(json_data.tax);
                    $('[id="'+flagid+'"]').val(json_data.flag);


                    var subtotal = ((json_data.tax * json_data.price)/100) + parseInt(json_data.price);
                    grandtotal = grandtotal + subtotal;
                    //console.log(subtotal);

                    $('[id="'+subtotalid+'"]').val(subtotal);
                    //$('#grandtotal').val(grandtotal);

                    //$("img").val();
                    //update_categories_from_subtype();
                    }
                    else
                    {
                        alert("SKU NOT FOUND");
                    }
                    getgrandtotal();        //getgrandtotal
                }
                });
                
                //getgrandtotal();   //get grandtotal;
            
            }

            function addsku(){

                var table = document.getElementById("items_table");
                var rowCount = table.rows.length;
                var row = table.insertRow(rowCount);
                row.id = "t"+rowCount;
                var cell1 = row.insertCell(0);
                var element1 = document.createElement("input");
                        element1.type = "text";
                        element1.name = "sku[]";
                        element1.className = "sku";
                        element1.id = "sku"+rowCount;
                        cell1.appendChild(element1);

                var cell2 = row.insertCell(1);
                var element2 = document.createElement("input");
                        element2.type = "text";
                        //element2.name = "name";
                        element2.className = "name";
                        element2.id = "name"+rowCount;
                        cell2.appendChild(element2);

                var cell3 = row.insertCell(2);
                var element3 = document.createElement("span");
                        //element3.type = "span";
                       // element3.name = "image";
                        element3.className = "image";
                        element3.id = "image"+rowCount;
                        cell3.appendChild(element3);

                var cell4 = row.insertCell(3);
                var element4 = document.createElement("input");
                        element4.type = "text";
                        //element4.name = "price";
                        element4.className = "price";
                        element4.id = "price"+rowCount;
                        cell4.appendChild(element4);


                var cell5 = row.insertCell(4);
                var element5 = document.createElement("input");
                        element5.type = "text";
                        //element5.name = "tax";
                        element5.className = "tax";
                        element5.id = "tax"+rowCount;
                        cell5.appendChild(element5);

                var cell6 = row.insertCell(5);
                var element6 = document.createElement("input");
                        element6.type = "text";
                        //element6.name = "subtotal";
                        element6.className = "subtotal";
                        element6.id = "subtotal"+rowCount;
                        cell6.appendChild(element6);

                var cell7 = row.insertCell(6);
                var element7 = document.createElement("input");
                        element7.type = "text";
                        //element6.name = "subtotal";
                        element7.className = "flag";
                        element7.id = "flag"+rowCount;
                        cell7.appendChild(element7);   


                 var cell8 = row.insertCell(7);
                 var element8 = document.createElement("button");
                        element8.type = "button";
                        element8.name = "deleterow";
                        element8.innerHTML = "deleterow";
						element8.className ="panel_button";
                        element8.id = "deleterow"+rowCount;
                        element8.onclick = function () { deleterow(this.id); };
                        cell8.appendChild(element8);             

                // var cell8 = row.insertCell(7);                          //insert cell after 7th cell of this particular row
                // var element8 = document.createElement("button");
                //         element8.type = "button";
                //         element8.name = "deleterow";
                //         element8.innerHTML = "deleterow";
                //         //element8.click = "deleterow(rowCount)";
                //         //element8.className = "flag";
                //         //element8.addEventListener('onclick', function(e) { alert("NIK"); }, false);
                //         element8.onclick = function () { deleterow("t", rowCount); }
                //         element8.id = "deleterow"+rowCount;
                //         cell8.appendChild(element8);                    //put this element in the cell
            }

            function deleterow(thisid) 
            {
                    
                    console.log(thisid);
                    var getindex = thisid.split("w");
                    newid = getindex[1];
                    //document.getElementById("items_table").deleteRow(0);
                    //var exrow = document.getElementById("t"+id);
                    //console.log("row: "+exrow);
                    //row.parentNode.removeChild(row);
                    $('#t' + newid).remove();

                    var table = document.getElementById("items_table");
                    var rowcount = table.rows.length;
                    console.log("rowcount: "+rowcount);
                    id = parseInt(newid);
                    var i = 0;

                    for(i=id; i<rowcount; i++  )
                    {

                        var nextid = i + 1;
                        console.log("nextid: "+ nextid);
                        document.getElementById("sku"+nextid).setAttribute("id", "sku"+i);
                        document.getElementById("name"+nextid).setAttribute("id", "name"+i);
                        document.getElementById("image"+nextid).setAttribute("id", "image"+i);
                        document.getElementById("price"+nextid).setAttribute("id", "price"+i);
                        document.getElementById("tax"+nextid).setAttribute("id", "tax"+i);
                        document.getElementById("flag"+nextid).setAttribute("id", "flag"+i);
                        document.getElementById("subtotal"+nextid).setAttribute("id", "subtotal"+i);

                        document.getElementById("deleterow"+nextid).setAttribute("id", "deleterow"+i);
                        document.getElementById("t"+nextid).setAttribute("id", "t"+i);

                        //console.log("in deleterow: "+skuid);
                    }

                    getgrandtotal();
                
            }

            function getgrandtotal(){

                var subtotalclass = document.getElementsByClassName("subtotal");
                var count = subtotalclass.length;
                var subtotal = 0;
                for(var i = 0; i<count; i++){
                    subtotal = subtotal + parseFloat(subtotalclass[i].value);
                    //console.log(subtotalclass[i].value);
                }

                console.log(subtotal);
                window.grandtotal = subtotal;
                $('#grandtotal').val(grandtotal);
            }

            function printinvoice(){
                var url = 'print_invoice.php';
                var fname = document.getElementById("firstname").value;
                var lname = document.getElementById("lastname").value;
                var email = document.getElementById("email").value;
                var phone = document.getElementById("phone").value;
                var payment = document.getElementById("payment").value;
                var grandtotal = document.getElementById("grandtotal").value;
                var skus  = $("input[name='sku\\[\\]']").map(function(){return $(this).val();}).get(); 
                var taxes  = $("input[class='tax']").map(function(){return $(this).val();}).get();
                var prices  = $("input[class='price']").map(function(){return $(this).val();}).get();
                var names  = $("input[class='name']").map(function(){return $(this).val();}).get();
                var subtotals  = $("input[class='subtotal']").map(function(){return $(this).val();}).get();
                url = url+'?firstname='+fname+'&lastname='+lname+'&email='+email+'&phone='+phone+'&payment='+payment+'&grandtotal='+grandtotal+'&skus='+skus+'&taxes='+taxes+'&prices='+prices+'&productnames='+names+'&subtotals='+subtotals;
                console.log("url:"+url);
                window.open(url , '_blank');
            }

            function emailinvoice(){
                var url = '/email_invoice.php';
                var fname = document.getElementById("firstname").value;
                var lname = document.getElementById("lastname").value;
                var email = document.getElementById("email").value;
                var phone = document.getElementById("phone").value;
                var payment = document.getElementById("payment").value;
                var grandtotal = document.getElementById("grandtotal").value;
                var skus  = $("input[name='sku\\[\\]']").map(function(){return $(this).val();}).get(); 
                var taxes  = $("input[class='tax']").map(function(){return $(this).val();}).get();
                var prices  = $("input[class='price']").map(function(){return $(this).val();}).get();
                var names  = $("input[class='name']").map(function(){return $(this).val();}).get();
                var subtotals  = $("input[class='subtotal']").map(function(){return $(this).val();}).get();
                url = url+'?firstname='+fname+'&lastname='+lname+'&email='+email+'&phone='+phone+'&payment='+payment+'&grandtotal='+grandtotal+'&skus='+skus+'&taxes='+taxes+'&prices='+prices+'&productnames='+names+'&subtotals='+subtotals;
                window.open(url , '_blank');

            }


            function getregionid(){
                console.log("here");
                console.log($('#region').val());
                $.ajax({
                type:'POST',
                
                url:'return_region_id.php',
                data:
                {
                    'region':$('#region').val()
                },
                async: false,
                
                success:function(data)
                {
                    
                    jQuery('#region_id').val(data); 

                }
                });
            }              

            $(document).ready(function(e) { 
                 getregionid();
            });
     



            </script>
        </head>
        <body id ="order_body">
			<h1>New Offline Order</h1>
		   
			<form action="order_created.php" target="_blank" method="POST">
				<fieldset>
			    <legend> Address: </legend> 
				<div>    
					Firstname : <input type= "text" name = "firstname" id="firstname" required = "true"> 
					Lastname : <input type= "text" name = "lastname" id="lastname" required = "true">
					email : <input type= "text" name = "email" id="email" required = "true">
					phone number: <input type="text" name = "phone" id="phone" required = "true">
				 </div>
				 <br>
				 <br>
				 <div id ="Address">   
					Street0: <input type="text" name="street0" value="offline Sale">
					Street1: <input type="text" name="street1" value="-">
					City: <input type="text" name="city" value = "New Delhi">
					Region:
					<select> 
					<option name = "region" id = "region" option value = "Delhi"> Delhi </option>
					</select>
					<input type="hidden" name = "region_id" id = "region_id">
					Pincode: <input type="text" name="pincode" value= "110065">
				  </div> 
			  </fieldset>
				<br>
                <fieldset>
					<legend>Payment Mode</legend>
						<input type="radio" id ="payment" name="payment_method" checked="checked" value="cashondelivery"> Cash On Delivery </input>
						<input type="radio" id = "payment" name="payment_method" value="moto"> Debit Card/ Credit Card </input>
				</fieldset>

            <br>
			<fieldset id ="product_fieldset">
				<legend>Product Details</legend>
				<table id="items_table">
					<tr>
						<th> SKU: </th>
						<th> Name: </th>
						<th> Image: </th>
						<th> Price: </th>
						<th>  Tax% : </th>
						<th> Subtotal: </th>
						<th> Stock: </th>
					</tr>    
					<tr id="t1">
					<td> <input type= "text" name = "sku[]" id="sku1" class="sku" required = "true"> </td>
					<td> <input type= "text" id="name1" class="name"> </td>
					<td><span id= "image1" class="image"></span></td>
					<td> <input type= "text" id="price1" class="price"> </td>
					<td> <input type= "text" id="tax1" class="tax"> </td>
					<td> <input type= "text" id="subtotal1" class="subtotal" required = "true"> </td>
					<td> <input type= "text" id="flag1" class="flag"> </td>
					<td> <button type= "button" id="deleterow1" class ="panel_button" style="display:none"> deleterow </button></td>
					</tr>
				</table>
				<button type="button" class ="panel_button" onclick="fetchdetails()"> fetchdetails</button>
				
				<br>
				<br>
				<button type = "button" class ="panel_button" onclick = "addsku()"> ADD SKU </button>
			</fieldset>
            <br>
            <fieldset>
			<legend>Order Summary</legend>
				Add comments: <input type= "text" name="comments" id="comments">
				<br>
				Grand Total in Rs: <input type="text" name="grandtotal" id="grandtotal">
				<br><br>
				
				<button id ="order_screenshot" class ="panel_button">Order Screenshot</button>
				<br>
				<button  id ="create_order" type = "submit" class ="panel_button" hidden = "true">Create Order</button>
			</fieldset>
            </form>
			<br>
                <button class ="panel_button" onclick="printinvoice()"> Print Invoice</button>
                <!--button class ="panel_button" onclick="emailinvoice()"> Email Invoice </button-->
				
				
        </body>
</html>

<style>

body
{
	background-color: #eaeaea;
	font-family: 'Avenir', sans-serif;
}

h1 
{
	text-align: center;
	color: #ffffff;
	background-color: #50c7c2;
	width:50%;
	margin-left:auto;
	margin-right:auto;
	font-family: 'Avenir', sans-serif;
}

fieldset
{
	
  max-width:560px;
  padding:16px;	
  text-align:center;
  margin:auto;

}

legend
{
	font-size: 1.2em;
	background-color :#323232;
	color : white;
	width:100%;

}

.panel_button
{
	color: white;
	background: #50c7c2;
	border: 2px outset #50c7c2;
	font-size:1.2em;
} 

</style>

<script type ="text/javascript">

 $(document).ready(function() {

    $("#order_screenshot").click(function() {

    var pdf = new jsPDF('p','pt','letter');
    var specialElementHandlers = {
    '#order_body': function (element, renderer) {
        return true;
        }
    };

    pdf.addHTML($('#order_body').first(), function() {
        pdf.save("order_details.pdf");
    });
	
	$("#create_order").toggle(true);  //shows button
    }
	
	);
});

</script>