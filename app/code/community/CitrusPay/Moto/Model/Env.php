<?php
class CitrusPay_Moto_Model_Env
{
	public function toOptionArray()
	{
		return array(
				array('value' => 'sandbox','label' => 'sandbox'),
				array('value' => 'staging','label' => 'staging'),
				array('value' => 'production','label' => 'production')
				);
	}
}