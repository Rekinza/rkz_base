<?php
class Loginradius_Sociallogin_Model_Source_VerticalSharing
{
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => '32', 'label' => '<img style="margin-right:5px" src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/images/Sharing/Vertical/32VerticlewithBox.png', array('_area' => 'adminhtml')) . '" /><br />');
        $result[] = array('value' => '16', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/images/Sharing/Vertical/16VerticlewithBox.png', array('_area' => 'adminhtml')) . '" /><br />');
        $result[] = array('value' => 'counter_vertical', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/images/Sharing/Vertical/verticalvertical.png', array('_area' => 'adminhtml')) . '" /><br />');
        $result[] = array('value' => 'counter_horizontal', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/images/Sharing/Vertical/verticalhorizontal.png', array('_area' => 'adminhtml')) . '" /><br />');
        return $result;
    }
}