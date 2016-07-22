<?php
class CitrusPay_Moto_Model_Yesno
{
	public function toOptionArray()
	{
		return array(
				array('value' => 'Y','label' => 'Y'),
				array('value' => 'N','label' => 'N'),
				);
	}
}