<?php
class Thredshare_Vendor_VendorController extends Mage_Core_Controller_Front_Action{
const PRODUCT_STATUS_RETURN=1;
const PRODUCT_STATUS_DONATE=2;
public function listAction(){
$action=$this->getRequest()->getParam('action');

if (Mage::getSingleton("customer/session")->isLoggedIn()){

//checking if person has become a seller
$email=Mage::getModel("customer/session")->getCustomer()->getEmail();
$vendor=Mage::getModel('vendor/info')->getVendor($email);
if ($vendor){
if ($action){
if ($action=="change_status"){
$product_id=$this->getRequest()->getParam('product_id');
$status=$this->attributeValueExists("rejected_after",$this->getRequest()->getParam('change_status'));
$change_status=$this->getRequest()->getParam('change_status');
if ($status && $product_id){

$product=Mage::getModel("catalog/product")->load($product_id);
if ($product){
$product->setRejectedAfter($status);
Mage::getModel("vendor/status")->setProductId($product_id)->setStatus($change_status)->save();
$product->save();
}

}
}
else if ($action=="change_price"){
$changes=$this->getRequest()->getParam('update_data');
if ($changes){
$changes=json_decode($changes);
foreach ($changes as $info){
$product_id=$info[0];
$price=$info[1];
if ($product_id && $price){

$product=Mage::getModel('catalog/product')->load($product_id);
if ($product){

$product->setSpecialPrice($price);
$product->save();
}
}
}
}
}
}
$this->loadLayout();
$this->renderLayout();
}else{
$this->_redirect("customer/account/index");
}
}else{
$this->_redirect("customer/account/login");
}
}
public function attributeValueExists($arg_attribute, $arg_value)
{
    $attribute_model        = Mage::getModel('eav/entity_attribute');
    $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;

    $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
    $attribute              = $attribute_model->load($attribute_code);

    $attribute_table        = $attribute_options_model->setAttribute($attribute);
    $options                = $attribute_options_model->getAllOptions(false);

    foreach($options as $option)
    {
        if ($option['label'] == $arg_value)
        {
            return $option['value'];
        }
    }

    return false;
}
public function newvendorAction(){

$customer=Mage::getModel("customer/session");
	if ($customer->isLoggedIn()){
		$this->loadLayout();
		$this->renderLayout();
	}else{
	
		$this->_redirect("customer/account/login");
		
	}
}
public function submitvendorAction(){

		$image = null;
        if (isset($_FILES['store_image']['name']) && $_FILES['store_image']['name'] != '') {
					try{
                    $uploader = new Varien_File_Uploader('store_image');
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png')); // or pdf or anything

                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);

                    $dir_name = 'vendor' . DS . 'images';
					$dir_path = Mage::helper('cartmart')->getImagesDir($dir_name);
					$ext=explode(".",$_FILES['store_image']['name']);
					$ext=$ext[1];
					if (!$ext){
						$ext="jpg";
					}
					$filename=time().".$ext";
					
					$uploader->save($dir_path, $filename);
                    $image = $filename;
					}catch(Exception $e){
					
					}
                }
                else{
                    $image = $this->getRequest()->getParam('old_image', false);
				}
  //check if customer is logged in && also check if vendor exists for same shop name
  $data = $this->getRequest()->getPost();
 
  if (Mage::getSingleton("customer/session")->isLoggedIn() && $data){
	 $isexists=false;
	$prevVendor=Mage::getModel("cartmart/profile")->load($data['store_name'],"shop_name");
	if ($prevVendor && $prevVendor->getEntityId()){
	
	$prevUser=Mage::getModel("admin/user")->load($prevVendor->getUserId());
	
		if ($prevUser && $prevUser->getEmail()){
			
			if (Mage::getModel("customer/session")->getCustomer()->getEmail()!=$prevUser->getEmail()){
				
				$isexists=true;
			}
		}
	}

   if (!$isexists){
   try{
		$vendor=new Varien_Object();
		$vendor->setShopName($data['store_name']);
		$vendor->setImage($image);
		$vendor->setNewPassword("thredshare21");
		$vendor->setFirstName($data['store_name']);
		$vendor->setBrand1($data['store_brand']);
		$vendor->setBrand2($data['store_brand2']);
		$vendor->setBrand3($data['store_brand3']);
		$vendor->setMessage($data['store_brand'].",".$data['store_brand2'].",".$data['store_brand3']);
		$vendor->setLastName($data['store_name']);
		$vendor->setUserName(Mage::getSingleton("customer/session")->getCustomer()->getEmail());
		$vendor->setEmail(Mage::getSingleton("customer/session")->getCustomer()->getEmail());
		$vendor->setNewPassword($data['new_password']);
		
		Mage::getModel("vendor/info")->saveVendor($vendor);
		$this->_redirect("*/*/list");
   }catch(Exception $e){
	Mage::getSingleton("core/session")->addError($e->getMessage());
   $this->_redirect("*/*/newvendor");
   }       
   }else{
   Mage::getSingleton('core/session')->addError("This shop name is already taken.Please select another one.");
   $this->_redirect("*/*/newvendor");
   }     
  }
  else{
  if (!Mage::getSingleton("customer/session")->isLoggedIn()){
  Mage::getSingleton('core/session')->addError("You need to be logged in to proceed"); 
  }
  else{
  Mage::getSingleton('core/session')->addError("Please fill the form correctly");
  }
  $this->_redirect("*/*/newvendor");
  //show waring message and redirect the customer to login page
  }
  
}
}

?>