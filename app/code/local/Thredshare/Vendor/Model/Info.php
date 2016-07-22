<?php 
class Thredshare_Vendor_Model_Info extends Mage_Core_Model_Abstract{

	public function isVendor($email){
	
	$vendor=Mage::getModel("admin/user")->load($email,"email");

	if ($vendor && $vendor->getUserId()){
	return true;
	}	
	else{
	return false;
	}
	}
	
	public function saveVendor($vendor){
		
          $model = Mage::getModel('admin/user');
		 
		  $user=$model->load($vendor->getEmail(),"email");
		  
		  if ($user && $user->getUserId()){
		  $user_id=$user->getUserId();
		  $profile=Mage::getModel('cartmart/profile')->load($user_id,"user_id");
		  if ($vendor->getImage()){
             $profile->setImage($vendor->getImage());
			 }
			 if ($vendor->getShopName()){
			$profile->setShopName($vendor->getShopName());
			}
			if ($vendor->getBrand1() && $vendor->getBrand2() && $vendor->getBrand3()){
			$profile->setMessage($vendor->getMessage());
			} 
		  $profile->save();
		  }
		  else{
		  $user_id=Mage::getModel('admin/user')->getCollection()->setOrder("user_id","DESC")->getLastItem()->getUserId();
		  $user_id++;
		  
		  $model->setUserId($user_id)
                        ->setData(array("username"=>$vendor->getUserName(),"firstname"=>$vendor->getFirstName(),
						"lastname"=>$vendor->getLastName(),"email"=>$vendor->getEmail(),
						"new_password"=>$vendor->getNewPassword()));
				

                if ($model->hasNewPassword() && $model->getNewPassword() === '') {
                    $model->unsNewPassword();
                }
                if ($model->hasPasswordConfirmation() && $model->getPasswordConfirmation() === '') {
                    $model->unsPasswordConfirmation();
                }                

                $result = $model->validate();

                if (is_array($result)) {
                
					if ($result[0]=="A user with the same user name or email aleady exists."){
					
					}
					else{
					throw new Exception("Error while validating");
					}
                }
				try{
                $model->save();
				}catch(Exception $e){
				
				}
				try{
                $role_id = Mage::helper('cartmart')->getConfig('general', 'vendor_role');

                $model->setRoleIds(array($role_id))
                        ->setRoleUserId($model->getUserId())
                        ->saveRelations();
				
                $model->save();
				}catch(Exception $e){
				
				}
				if (!$model->getUserId()){
				$model->setUserId($user_id);
				}
                $profileCollection = Mage::getModel('cartmart/profile')
                        ->getCollection()
                        ->addFieldToFilter('user_id', $model->getUserId());

                if ($profileCollection->count() > 0)
                    $profile = Mage::getModel('cartmart/profile')->load($profileCollection->getFirstItem()->getId());
                else
                    $profile = Mage::getModel('cartmart/profile')
                            ->setTotalAdminCommission(0)
                            ->setTotalVendorAmount(0)
                            ->setTotalVendorPaid(0);

                if (!is_null($vendor->getImage()))
                    $profile->setImage($vendor->getImage());
				
                $profile->setUserId($model->getUserId())
						->setShopName($vendor->getShopName())
						->setMessage($vendor->getMessage())
						->setContactNumber($vendor->getContactNumber)
						->setCountry($vendor->getCountry())						
                        ->setAdminCommissionPercentage($vendor->getAdminCommissionPercentage());
				
				$profileOrder = $vendor->getProfileOrder();
                if(!empty($profileOrder)){$profile->setProfileOrder($profileOrder);}

                $featured = $vendor->getFeatured();
                if(in_array($featured,array('0','1'))){$profile->setFeatured($featured);}
                        
                Mage::dispatchEvent('vendor_profile_save_before', array('profile' => $profile, 'post_data' => $vendor));
                
				$profile->save();
                        
				
             }
            
	
	}
	
	public function getVendor($email){
		
		$user_id=Mage::getModel("admin/user")->load($email,"email");
		if ($user_id){
		
		$vendor=Mage::getModel('cartmart/profile')->load($user_id->getUserId(),"user_id");
		return $vendor;
		
		}else{
		return null;
		}
	
	}
	
	public function addPointsForVendorUserId($points,$userId){
		
		if ($userId && $points){
			$customer=Mage::getModel('admin/user')->load($userId);
		if ($customer && $customer->getEmail()){
			$customerEmail=Mage::getModel("customer/customer")->setWebsiteId(1)->loadByEmail($customer->getEmail());

		if ($customerEmail && $customerEmail->getId()){
		
			$vendorRewardPoint=Mage::getModel('rewardpoints/customer')->load($customerEmail->getId());
			if (!$vendorRewardPoint || !$vendorRewardPoint->getCustomerId()){
			
			$vendorRewardPoint=Mage::getModel('rewardpoints/customer');
			$vendorRewardPoint->setCustomerId($customerEmail->getId());
			}
			
			if ($vendorRewardPoint->getMwRewardPoint()){
			$vendorRewardPoint->setMwRewardPoint($vendorRewardPoint->getMwRewardPoint()+$points);
			}else{
			$vendorRewardPoint->setMwRewardPoint($points);
			}
			
			$vendorRewardPoint->save();
		}
		}
		
		}
		

	}


}
?>