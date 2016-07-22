<?php

umask(0);

require_once 'OrderGenerator.php';
require_once 'CustomerGenerator.php';
require_once '../../app/Mage.php';
include '../db_config.php';
include '../utils/access_block.php';

Mage::app();

echo "create order";
?>

<html>
        <head>
            <script src="jquery-1.11.1.js"></script>
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
                var element3 = document.createElement("input");
                        element3.type = "text";
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
                    console.log(subtotalclass[i].value);
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
        <body>
           <div>
           <b> Address: </b> 
            <form action="order_created.php" method="POST">
            <div>    
                Firstname : <input type= "text" name = "firstname" id="firstname"> 
                Lastname : <input type= "text" name = "lastname" id="lastname">
                email : <input type= "text" name = "email" id="email">
                phone number: <input type="text" name = "phone" id="phone">
             </div>
             <br>
             <br>
             <div>   
                Street0: <input type="text" name="street0" value="offline Sale">
                Street1: <input type="text" name="street1" value="Square one Mall, Saket">
                City: <input type="text" name="city" value = "New Delhi">
                Region:
                <select> 
                <option name = "region" id = "region" option value = "Delhi"> Delhi </option>
                </select>
                <input type="hidden" name = "region_id" id = "region_id">
                Pincode: <input type="text" name="pincode" value= "110023">
              </div>  
                <br>
                <br>
                <input type="radio" id ="payment" name="payment_method" checked="checked" value="cashondelivery"> Cash On Delivery </input>
                <input type="radio" id = "payment" name="payment_method" value="moto"> Debit Card/ Credit Card </input>

            <br>
            <br>
            <br>
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
                <td> <input type= "text" name = "sku[]" id="sku1" class="sku"> </td>
                <td> <input type= "text" id="name1" class="name"> </td>
                <td> <input type= "text" id= "image1" class="image"> </td>
                <td> <input type= "text" id="price1" class="price"> </td>
                <td> <input type= "text" id="tax1" class="tax"> </td>
                <td> <input type= "text" id="subtotal1" class="subtotal"> </td>
                <td> <input type= "text" id="flag1" class="flag"> </td>
                <td> <button type= "button" id="deleterow1" style="display:none"> deleterow </button></td>
                </tr>
            </table>
            <button type="button" onclick="fetchdetails()"> fetchdetails</button>
            
            <br>
            <br>
            <button type = "button" onclick = "addsku()"> ADD SKU </button>
            <br>
            <br>
            Add comments to the Order: <input type= "text" name="comments" id="comments">

            Grand Total: <input type="text" name="grandtotal" id="grandtotal">
            
            <input type = "submit" value="Create Order">
            </form>
            <div>
                <button onclick="printinvoice()"> Print Invoice</button>
                <button onclick="emailinvoice()"> Email Invoice </button>
            </div>
        </body>
<!-- <?php
// $product = Mage::getModel('catalog/product');
// $id = Mage::getModel('catalog/product')->getResource()->getIdBySku('c-su-lak-01');

//     $product->load($id); ?>
    
   <!--<img src= "<?php echo Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(185, 256); ?>"> 
    
//     <?php
    
?> -->  

</html>