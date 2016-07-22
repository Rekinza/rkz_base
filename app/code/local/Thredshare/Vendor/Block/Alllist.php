<?php
class Thredshare_Vendor_Block_Alllist extends Mage_Catalog_Block_Product_List {
	const VENDOR_MAX_SIZE=20;
	public $vendors=null;
	public function getVendorsCollection($after){
	
	if (!$this->vendors){
	if (!$after){
	$after=0;
	}
	$this->vendors=$this->_getVendorCollection($after);
	}
	return $this->vendors;
	
	}
	
	protected function _getVendorCollection($after)
    {	$resource=Mage::getSingleton("core/resource");
		$read_con=$resource->getConnection("core_read");
		$collection=null;
		if (!$after){
		$after=0;
		}
	
		$prev=$after*9;
		
		$query="SELECT user_id FROM `admin_user` as a join 
		`catalog_product_entity_int` as c 
		on a.user_id=c.value
		join catalog_product_index_price as d on c.entity_id=d.entity_id
		join `cataloginventory_stock_status` as e
		on c.entity_id=e.product_id
		where d.customer_group_id=1
		and d.final_price>0
		and e.stock_status=1
		and c.`attribute_id`=132 and c.`entity_type_id`=4 
		and c.value is not null and a.user_id>0
		group by a.user_id
		order by a.user_id asc limit ".$prev.",9";
		$vendor = $read_con->fetchAll($query);
	
		foreach ($vendor as $ven){
			$ven_ids[]=$ven['user_id'];
		}
	
		if (count($ven_ids)){
		$max_ven_id=$ven_ids[count($ven_ids)-1];
		$min_ven_id=$ven_ids[0];
		}
		if (!$min_ven_id){
		$min_ven_id=1000000000000;
		}
		if (!$max_ven_id){
		$max_ven_id=1000000000000;
		}
		
		$query="SELECT * FROM `admin_user` as a join 
		(select c.`value`,c.`entity_id` as product_id from `catalog_product_entity_int` as c left join `catalog_product_entity_int` as 
		d on c.value=d.value 
		join catalog_product_index_price as f on c.entity_id=f.entity_id
		join `cataloginventory_stock_status` as e
		on c.entity_id=e.product_id
		join `cataloginventory_stock_status` as g
		on d.entity_id=g.product_id
	    join catalog_product_index_price as h on d.entity_id=h.entity_id
		where f.customer_group_id=1
		and h.customer_group_id=1
		and f.final_price>0
		and h.final_price>0
		and g.stock_status=1
		and e.stock_status=1
		and c.`attribute_id`=132 and c.`entity_type_id`=4 AND d.`attribute_id`=132 
		and d.`entity_type_id`=4
		and c.value is not null and d.value is not null and c.value>=$min_ven_id and d.value>=$min_ven_id
		and c.value<=$max_ven_id and d.value<=$max_ven_id and c.entity_id>=d.entity_id
		group by c.entity_id having count(*)<=3 ) 
		as b on a.user_id=b.value 
		join openwriter_cartmart_profile as c on a.user_id=c.user_id
		order by a.user_id asc";

		$collection=$read_con->fetchAll($query);
		$product_ids=array();
		$vendors=array();
		
		foreach ($collection as $col){
	
		if (!$vendors[$col['user_id']]){
		$vendors[$col['user_id']]=$col;
		$vendors[$col['user_id']]['product']=array();
		}
			$product_ids[]=$col['product_id'];
		}
		
		
		$collection=Mage::getResourceModel("catalog/product_collection")
		->addAttributeToSelect("*")->addAttributeToSelect("vendor")->
		addAttributeToFilter("entity_id",array("in"=>$product_ids));


		foreach ($collection as $col){

		if (!$vendors[$col->getVendor()]['product']){
		$vendors[$col->getVendor()]['product']=array();
		}
		
		$vendors[$col->getVendor()]['product'][]=$col;
		}
		return $vendors;
		
	}
	
	public function getVendorUrl($vendor){
	
	return Mage::getBaseUrl()."/cartmart/vendor/profile/id/{$vendor['entity_id']}/";
	
	}
	
	public function getVendorImage($vendor){
	
	return Mage::getBaseUrl()."/media/cartmart/vendor/vendor/images/{$vendor['image']}";
	
	}
	
	public function getNextPageUrl(){
	if (Mage::app()->getRequest()->getParam('after')){
	$after=Mage::app()->getRequest()->getParam('after')+1;
	}else{
	$after=1;
	}
	if ($this->vendors && count($this->vendors)){
	return "http://www.rekinza.com/sellers?after=$after";
	}else{
	return "http://www.rekinza.com/sellers?after=$after";
	}
	}
}
?>
