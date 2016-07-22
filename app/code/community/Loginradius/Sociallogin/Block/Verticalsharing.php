<?php
class Loginradius_Sociallogin_Block_Verticalsharing extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    private $loginRadiusVerticalSharing;

    public function __construct()
    {
        $this->loginRadiusVerticalSharing = new Loginradius_Sociallogin_Block_Sociallogin();
    }

    protected function _toHtml()
    {
        $content = "";
        if ($this->loginRadiusVerticalSharing->verticalShareEnable() == "1") {
            $content = "<div class='loginRadiusVerticalSharing'></div>";
        }
        return $content;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
}