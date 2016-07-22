<?php
include '../db_config.php';
include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';

$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if ($numresult > 0)
{
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
	$blockname = "pickup panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{
	$email_id = $_GET['email_id'];
	$first_name = $_GET['first_name'];
	$last_name = $_GET['last_name'];
	$mobile = $_GET['mobile'];
	$pickup_id = $_GET['pickup_id'];
	
	?>
	<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
	
	<?php
	require_once '../../app/Mage.php';
	Mage::app();
	
	
	if( $email_id )
	{
		
		//Check if customer ID exists in customer table
		$customer = Mage::getModel("customer/customer"); 
		$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
		$customer->loadByEmail($email_id); //load customer by email id
	
		$customer_id = $customer->getId();
					

		if ($customer_id)
		{
			echo "<br> Customer exists in Customer table <br>";
		}
		
		
		else  //Create new customer in Magento
		{
			if ($first_name)
			{
				$websiteId = Mage::app()->getWebsite()->getId();
				$store = Mage::app()->getStore();
				

				 
				$customer = Mage::getModel("customer/customer");
				$customer   ->setWebsiteId($websiteId)
							->setStore($store)
							->setFirstname($first_name)
							->setLastname($last_name)
							->setEmail($email_id)
							->setPassword($first_name.$last_name);
				 
				try{
					$customer->save();
					$session = Mage::getSingleton('customer/session');
            		$customer->sendNewAccountEmail('confirmation',$session->getBeforeAuthUrl(),$storeId); //send confirmation email to customer?
					$session->logout();

					//Initiate and Set Customer Reward Points to 0
					$customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->loadByEmail($email_id);
					//var_dump($customer);
					if($customer->getId())
								{
									
									Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($customer->getId(), 0);
								}
					$customer->save();
					echo "New customer created <br>";
				}
				catch (Exception $e) {
					Zend_Debug::dump($e->getMessage());
				}
			}
			else
			{
				echo "Name is blank <br>";
			}
		}
		
		
		//Check if customer exists as vendor 
		
		$query = "SELECT user_id FROM admin_user where email = '$email_id'";
		$result = mysql_query($query);
		
		$numresult = mysql_numrows($result);

		if ( $numresult > 0 )
		{
			echo "Vendor ID already exists <br>";
		}
		else  		//Else create vendor

		{
			
				$user = Mage::getModel("admin/user");
	
				$user->setUsername($email_id)
					->setFirstname($first_name)
					->setLastname($last_name)
					->setPassword($first_name.$last_name)
					->setEmail($email_id)
					->setIsActive(1);
					
				/*if ($this->getRequest()->getParam('password', false)) {
					$user->setNewPassword($this->getRequest()->getParam('password', false));
				}

				if ($this->getRequest()->getParam('confirmation', false)) {
					$user->setPasswordConfirmation($this->getRequest()->getParam('confirmation', false));
				}
				*/
				$result = $user->validate();
				if (is_array($result)) {
					
					foreach($result as $error) {
						Mage::getSingleton('core/session')->addError($error);
						
					}
				}
					//Mage::getSingleton('core/session')->setTestData($data);
					//$this->_redirect('*/*/register');
					//return;
				
					$user->save();
					
					$role_id = Mage::helper('cartmart')->getConfig('general', 'vendor_role');

	                $user->setRoleIds(array($role_id))
						->setRoleUserId($user->getUserId())
						->saveRelations();

	                $user->save();
	                
	                $profile = Mage::getModel('cartmart/profile')
						->setTotalAdminCommission(0)
						->setTotalVendorAmount(0)
						->setTotalVendorPaid(0);                
	                 
					$shopname = $first_name[0].$last_name[0].$pickup_id." Closet";
					echo "Shop Name is ".$shopname."<br/>";
					$country = 'IN';
					$commission = 0;

	                $profile->setUserId($user->getUserId())
						->setShopName($shopname)
						->setCountry($country)   						
						->setContactNumber($mobile)
						->setAdminCommissionPercentage($commission)
						->save();

					echo "Vendor account created";
				
			}
		}
		
		else
		{
			echo "Blank email ID";
		}
		
		mysql_close();
	} //panelif ends
	else {
		echo "not allowed for this email id";
	}
} //strcmp ends
else {
	echo "Sorry! Not authorized.";
}
?>