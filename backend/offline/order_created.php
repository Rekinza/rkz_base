<?php

umask(0);

require_once 'OrderGenerator.php';
require_once 'CustomerGenerator.php';
require_once '../../app/Mage.php';
include '../db_config.php';

include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';

Mage::app();

$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if ($numresult > 0)
{
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
	$blockname = "offline panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{

?>

<html>
<head>
  <script src="jquery-1.11.1.js"></script>
  <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
  <script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>
</head>
<body>

<?php
$num = count($_POST);
 $customer_fname = $_POST['firstname'];
 $customer_lname = $_POST['lastname'];
 $email = $_POST['email'];
 $phone = $_POST['phone'];
 $grandtotal = $_POST['grandtotal'];
 
// echo $phone;
// echo $grandtotal;




 //$address = $_POST['address'];
 $street0 = $_POST['street0'];
 $street1 = $_POST['street1'];
 $city = $_POST['city'];
 $region = $_POST['region'];
 $region_id = $_POST['region_id'];
 $postcode = $_POST['pincode'];

 $payment_method = $_POST['payment_method'];
 echo $payment_method;
 $ordercomments = $_POST['comments'];
 var_dump($ordercomments);


 $namecount = count($_POST['sku']);
 $allskus = $_POST['sku'];
 var_dump($allskus);

 $allprices = array();
 $alltaxes = array();
 $allproductnames = array();
 $allsubtotals = array();

 $orderarray = array();
 for ($i=0; $i<$namecount; $i++){
      $singleku = $allskus[$i];
      $id = Mage::getModel('catalog/product')->getResource()->getIdBySku($singleku);
      $pusharray = array(
            'product' => $id,
            'qty' => 1
        );
      $product = Mage::getModel('catalog/product');
      $product->load($id);
      $price = $product->getSpecialPrice();
      $productname = urlencode($product->getName());
      mage::log($productname);
      $taxClassId = $product->getData("tax_class_id");
      $taxClasses = Mage::helper("core")->jsonDecode(Mage::helper("tax")->getAllRatesByProductClass());
      $taxRate = $taxClasses["value_".$taxClassId];


      $productsubtotal = (($taxRate * $price)/100) + $price ;

      array_push($orderarray, $pusharray);
      array_push($allprices, $price);
      array_push($alltaxes, $taxRate);
      array_push($allproductnames, $productname);
      array_push($allsubtotals, $productsubtotal);
 }
 $parsedskus = implode(",", $allskus);
 $parsedprices = implode(",", $allprices);
 $parsedtaxes = implode(",", $alltaxes);
 $parsedproductnames = implode(",", $allproductnames);
 $parsedproductsubtotals = implode(",", $allsubtotals);

 create_customers_with_order(1, $customer_fname, $customer_lname, $email, $phone, $orderarray, $namecount, $allskus , $street0, $street1, $city, $region, $region_id, $postcode, $payment_method, $ordercomments, $parsedproductsubtotals); //calling function to create order


?>


  <div>
    <br>
    <b> Order Created!</b>
    <div>
      Firstname : <input type= "text" name = "firstname" id = "firstname" value = "<?php echo $customer_fname?>"> <br/>
      Lastname : <input type= "text" name = "lastname" id = "lastname" value = "<?php echo $customer_lname?>"> <br/>
      Email : <input type= "text" name = "email" id = "email" value = "<?php echo $email?>"> <br/>
      Phone : <input type="text" name = "phone" id = "phone" value = "<?php echo $phone ?>" > <br/>
      Payment : <input type="text" name = "payment" id = "payment" value = "<?php echo $payment_method ?>" > <br/>
      Grand Total : <input type="text" name = "grandtotal" id = "grandtotal" value = "<?php echo $grandtotal ?>" > <br/>

    </div>
    <br>
    <div>
      <?php echo '<button onclick = "printinvoice(\''.$parsedskus.'\' ,  \''.$parsedprices.'\' , \''.$parsedtaxes.'\' , \''.$parsedproductnames.'\' , \''.$parsedproductsubtotals.'\')">Print Invoice</button>' ?>
      <?php //echo '<button onclick = "emailinvoice(\''.$parsedskus.'\' ,  \''.$parsedprices.'\' , \''.$parsedtaxes.'\' , \''.$parsedproductnames.'\')"> Email Invoice</button>' ?>
    </div>
  </div> 

</body>
</html>
<?php

	} //panelif ends
	else
	{
		echo "not allowed for this email id";
	}
} //numrows ends
else 
{
	echo "Sorry! Not authorized.";
}

function create_customers_with_order($qty, $aa, $nn, $email, $phone, $itemarray, $namecount, $allskus , $street0, $street1, $city, $region, $region_id, $postcode, $payment_method, $ordercomments)
{
    $orderGenerator = new OrderGenerator();
    $customerGenerator = new CustomerGenerator();

    for ($i = 0; $i < $qty; $i++)
    {
        $customer = $customerGenerator->createCustomer(array(
                'account' => array(
                    'firstname' => $aa,
                    'lastname' => $nn,
                    'email' => $email,
                ),
                'address' => array(
                    '_item1' => array(
                    'prefix' => '',
                    'firstname' => $aa,
                    'middlename' => '',
                    'lastname' => $nn,
                    'suffix' => '',
                    'company' => '',
                    'street' => array(
                        0 => 'Address',
                        1 => '',
                    ),
                    'city' => 'City',
                    'country_id' => '',
                    'region_id' => '',
                    'region' => '',
                    'postcode' => '',
                    'telephone' => $phone,
                    'fax' => '',
                    'vat_id' => '',
                    ),
                ),
            ));

        //mage::log($customer);
        //exit(0);
        $orderGenerator->setPaymentMethod($payment_method);
        $orderGenerator->setCustomer($customer);

        $orderGenerator->createOrder($itemarray, $street0, $street1, $city, $region, $region_id, $postcode, $ordercomments );
    }

     //now to reduce quantity of products

    for($i = 0; $i< $namecount; $i++){
      $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$allskus[$i]);
        if($product){               
                        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product); // Load the stock for this product
                        if($stock->getQty() > 0)
						{						
							$stock->setQty($stock->getQty() - 1); // Set to new Qty           
							$stock->save(); // Save
						}
                    }
      }              

}



?>


<script type = "text/javascript">

function printinvoice(parsedskus, parsedprices, parsedtaxes, parsedproductnames, parsedproductsubtotals){
  console.log(parsedskus);
  console.log(parsedprices);
  console.log(parsedtaxes);
       var url = 'print_invoice.php';
       var fname = document.getElementById("firstname").value;
       var lname = document.getElementById("lastname").value;
       var email = document.getElementById("email").value;
       var phone = document.getElementById("phone").value;
       var payment = document.getElementById("payment").value;
       var grandtotal = document.getElementById("grandtotal").value;
       //url = url + parsedskus;
       //url = url+'?firstname='+fname+'&lastname='+lname+'&email='+email+'&skus='+parsedskus+'&taxes='+parsedtaxes+'&prices='+parsedprices+'&productnames='+parsedproductnames;
       url = url+'?firstname='+fname+'&lastname='+lname+'&email='+email+'&phone='+phone+'&payment='+payment+'&grandtotal='+grandtotal+'&skus='+parsedskus+'&taxes='+parsedtaxes+'&prices='+parsedprices+'&productnames='+parsedproductnames+'&subtotals='+parsedproductsubtotals;
                
       console.log("url:"+url);
       window.open(url , '_blank');
       }

function emailinvoice(parsedskus, parsedprices, parsedtaxes, parsedproductnames) {
       
       var url = 'backend/offline/email_invoice.php';
       var fname = document.getElementById("firstname").value;
       var lname = document.getElementById("lastname").value;
       var email = document.getElementById("email").value;
       //url = url + parsedskus;
       url = url+'?firstname='+fname+'&lastname='+lname+'&email='+email+'&skus='+parsedskus+'&taxes='+parsedtaxes+'&prices='+parsedprices+'&productnames='+parsedproductnames;
       console.log("url:"+url);
       window.open(url , '_blank');
       }       

</script>
<style>
body
{
  background-color: #eaeaea;
    font-family: 'Avenir', sans-serif;
    width: 80%;
    margin-left:auto;
    margin-right: auto;
    font-size: 12px;
}

h1 
{
    text-align: center;
    color: #323232;
    margin-left:auto;
    margin-right:auto;
    font-family: 'Avenir', sans-serif;
    font-size: 32px;
}

span
{
    font-family: 'Avenir', sans-serif;
}

div
{
    line-height:2em;
    font-family: 'Avenir', sans-serif;
}

legend
{
    font-size: 1em;
    background-color :#323232;
    color : white;
    width:80%;
    font-family: 'Avenir', sans-serif;

}

button
{
  color: white;
  background: #50c7c2;
  border: 2px outset #d7b9c9;
  display:inline-block;
    font-family: 'Avenir', sans-serif;
    margin: 20px;
    font-size: 12px;
} 

table
{
    margin-left:auto;
    margin-right:auto;
    width:80%;
    text-align:center;
    line-height:2em;
    font-family: 'Avenir', sans-serif;
}

.cust-info
{
    text-align: left;
    display: inline-block;
    width:48%;
}
.address
{
    text-align: left;
    display: inline-block;
    width:48%;
}

input
{
    line-height: 2em;
}
input[type="submit"]
{
    color: white;
    background: #50c7c2;
}


</style>