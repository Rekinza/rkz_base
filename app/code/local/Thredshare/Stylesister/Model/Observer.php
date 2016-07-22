<?php
class Thredshare_Stylesister_Model_Observer{
public function fss($observer) {
 
if ($observer->getEvent()->getStatus().""!="really_confirmed"){
     return;
}

$order = $observer->getEvent()->getOrder();
$store=$order->getStoreId();
$CustomerEmail = $order->getCustomerEmail();

if($order->getCustomerId() == NULL){
               $customer_name = $order->getBillingAddress()->getFirstname();              
} 
else {
                $cust = Mage::getModel('customer/customer')->load($order->getCustomerId());
                $customer_name =  $cust->getFirstname();
}

$items = $order->getAllVisibleItems();


foreach($items as $i){

    $status = $i->getStatus();
    if($status != 'Refunded')
    {
        $vendorId=Mage::getModel("catalog/product")->load($i->getProductId())->getVendor();
        $producttype=Mage::getModel("catalog/product")->load($i->getProductId())->getCategoryIds();
        $entityid = Mage::getModel('cartmart/profile')->load($vendorId, 'user_id')->getEntityId();
        
        $productIds = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('vendor', $vendorId) 
            ->getAllIds();

         $count = 0;
         foreach($productIds as $product) {

            $_product = Mage::getModel('catalog/product')->load($product);
            $categorycheck = $_product->getCategoryIds();
            if ($categorycheck[1] == $producttype[1]) {

                    $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product);
                    if ($stock->getIsInStock() == 1){
                            $count++;
                    }
            }
          }  

         $numberofproducts[$entityid] = $count; //here also changed from userid
    }
}       


  $buyermodel = Mage::getModel("thredshare_stylesister/stylesister")->getCollection();
  foreach ($buyermodel as $buyer)
     {
            $mailingid = $buyer->getCustomerEmail();
            if ($mailingid === $CustomerEmail) //check if you find email
            {
                $id = $buyer->getKinzasisterId();
                $fetchedmap = $buyer->getSellerMap(); //get the map if you find it
                $flag = 1;
                break;
                
            }
     }

  if($flag == 1) {
   
            $sellerarray = explode("|", $fetchedmap);
            foreach ($numberofproducts as $key => $value) {
                   foreach ($sellerarray as $seller) {
                           if($seller == $key)             //if the value of one array matches the key of the other
                                 {
                                     $numberofproducts[$key] = 0; //If already exists in the map, donot send email again for
                                 }                                //this seller and make the value 0 so it doesnt pass the next filter.
                        
                      }
              } 
    }          


$maxvalue = max($numberofproducts);  //since the keys are vendorids of the associative array, it will check the values only.
$maxkey = array_keys($numberofproducts, max($numberofproducts));
$mailedvendorid = $maxkey[0];
   if ($maxvalue > 2) {
        //send email

            $date = Mage::getModel('core/date')->date("m/d/Y", $date);
            $storeId = $store;
            $emailId = "hello@rekinza.com";
            $mailTemplate = Mage::getModel('core/email_template');
            $mailTemplate->addBcc('hello@rekinza.com');
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
                ->setReplyTo($emailId);
            $mailTemplate->sendTransactional( 34,
            array('name'=>"REKINZA","email"=>$emailId),
            $CustomerEmail,
            $customer_name,
            array(
            'customer'  =>$customer_name,
            'vendor'=> "http://www.rekinza.com/cartmart/vendor/profile/id/".$mailedvendorid."/?utm_source=stylesister&utm_medium=email&utm_campaign=stylesister",
            )
            );

            if (!$mailTemplate->getSentSuccess()) {
                Mage::logException(new Exception('Cannot send stylesister mail'));
                var_dump("Cannot send stylesister mail");
            }

 
            if($flag == 0){
                if($mailedvendorid != NULL && $CustomerEmail != NULL){
                
                Mage::getModel("thredshare_stylesister/stylesister")->saveSisters($mailedvendorid, $CustomerEmail);
                
                }
            }
            else{
                $model = Mage::getModel("thredshare_stylesister/stylesister")->load($id);
                try {
                    $model->setKinzasisterId($id)->setSellerMap($fetchedmap.$mailedvendorid."|")->setDate($date)->save();
                    echo "Data appended successfully.";
       
                } catch (Exception $e){
                  echo $e->getMessage(); 
                  }      
            }

    }
}
}
?>