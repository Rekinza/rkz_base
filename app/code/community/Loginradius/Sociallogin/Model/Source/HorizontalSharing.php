<?php
class Loginradius_Sociallogin_Model_Source_HorizontalSharing
{
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => '32', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/images/Sharing/Horizontal/horizonSharing32.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => '16', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/images/Sharing/Horizontal/horizonSharing16.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'single_large', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/images/Sharing/Horizontal/single-image-theme-large.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'single_small', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/images/Sharing/Horizontal/single-image-theme-small.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'counter_vertical', 'label' => '<img src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/images/Sharing/Horizontal/vertical.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        $result[] = array('value' => 'counter_horizontal', 'label' => '<img style="width:375px;" src="' . Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/images/Sharing/Horizontal/horizontal.png', array('_area' => 'adminhtml')) . '" /><div style="clear:both"></div>');
        return $result;
    }
}