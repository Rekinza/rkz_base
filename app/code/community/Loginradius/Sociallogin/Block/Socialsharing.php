<?php
class Loginradius_Sociallogin_Block_Socialsharing extends Mage_Core_Block_Template
{
    /**
     * function returns script required for vertical sharing.
     *
     * global $loginRadiusSettings;
     */
    public static function login_radius_sharing_get_sharing_script_vertical($loginRadiusSettings, $apiKey)
    {
        $sharingScript = '';
        $verticalThemvalue = isset($loginRadiusSettings['verticalSharingTheme']) ? $loginRadiusSettings['verticalSharingTheme'] : '';

        switch ($verticalThemvalue) {
            case '32':
                $size = '32';
                $interface = 'Simplefloat';
                $sharingVariable = 'i';
                break;

            case '16':
                $size = '16';
                $interface = 'Simplefloat';
                $sharingVariable = 'i';
                break;

            case 'counter_vertical':
                $sharingVariable = 'S';
                $ishorizontal = 'false';
                $interface = 'simple';
                $type = 'vertical';
                break;

            case 'counter_horizontal':
                $sharingVariable = 'S';
                $ishorizontal = 'false';
                $interface = 'simple';
                $type = 'horizontal';
                break;

            default:
                $size = '32';
                $interface = 'Simplefloat';
                $sharingVariable = 'i';
                break;
        }

        $verticalPosition = isset($loginRadiusSettings['verticalAlignment']) ? $loginRadiusSettings['verticalAlignment'] : '';
        switch ($verticalPosition) {
            case "top_left":
                $position1 = 'top';
                $position2 = 'left';
                break;

            case "top_right":
                $position1 = 'top';
                $position2 = 'right';
                break;

            case "bottom_left":
                $position1 = 'bottom';
                $position2 = 'left';
                break;

            case "bottom_right":
                $position1 = 'bottom';
                $position2 = 'right';
                break;

            default:
                $position1 = 'top';
                $position2 = 'left';
                break;
        }

        $offset = '$' . $sharingVariable . '.' . $position1 . ' = \'0px\'; $' . $sharingVariable . '.' . $position2 . ' = \'0px\';';

        if (empty($size)) {
            $providers = self:: get_counter_providers('vertical', $loginRadiusSettings);
            $sharingScript .= 'LoginRadius.util.ready( function() { $SC.Providers.Selected = ["' . $providers . '"]; $S = $SC.Interface.' . $interface . '; $S.isHorizontal = ' . $ishorizontal . '; $S.countertype = \'' . $type . '\'; ' . $offset . ' $u = LoginRadius.user_settings; if(typeof document.getElementsByName("viewport")[0] != "undefined"){$u.isMobileFriendly=true;}; $S.show( "loginRadiusVerticalSharing" ); } );';
        } else {
            $providers = self:: get_sharing_providers('vertical', $loginRadiusSettings);
            // prepare sharing script
            $sharingScript .= 'LoginRadius.util.ready( function() { $i = $SS.Interface.' . $interface . '; $SS.Providers.Top = ["' . $providers . '"]; $u = LoginRadius.user_settings;';
            $sharingScript .= '$u.apikey= \'' . trim($apiKey) . '\';';
            $sharingScript .= '$i.size = ' . $size . '; ' . $offset . ' if(typeof document.getElementsByName("viewport")[0] != "undefined"){$u.isMobileFriendly=true;}; $i.show( "loginRadiusVerticalSharing" ); } );';
        }
        return $sharingScript;
    }

    /**
     * function returns comma seperated counters lists
     *
     * global $loginRadiusSettings;
     */
    public function get_counter_providers($themeType, $loginRadiusSettings)
    {
        $searchOption = $themeType . 'CounterProvidersHidden';
        if (!empty($loginRadiusSettings[$searchOption])) {
          return str_replace(',', '","', $loginRadiusSettings[$searchOption]);
        } else {
            return 'Facebook Like","Google+ +1","Pinterest Pin it","LinkedIn Share","Hybridshare';
        }
    }

    /**
     * function returns comma seperated sharing providers lists
     *
     * global $loginRadiusSettings;
     */
    public static function get_sharing_providers($themeType, $loginRadiusSettings)
    {
        $searchOption = $themeType . 'SharingProvidersHidden';
        if (!empty($loginRadiusSettings[$searchOption])) {
          return str_replace(',', '","', $loginRadiusSettings[$searchOption]);
        } else {
            return 'Facebook","Twitter","Pinterest","Print","Email';
        }
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getHorizontalSharingSettings()
    {
        return Mage::getStoreConfig('sociallogin_options/horizontalSharing');
    }

    public function getVerticalSharingSettings()
    {
        return Mage::getStoreConfig('sociallogin_options/verticalSharing');
    }

    /**
     * function returns script required for horizontal sharing.
     *
     * global $loginRadiusSettings;
     */
    public function login_radius_sharing_get_sharing_script_horizontal($loginRadiusSettings, $apikey)
    {
        $size = '';
        $sharingScript = '';
        $horizontalThemvalue = isset($loginRadiusSettings['horizontalSharingTheme']) ? $loginRadiusSettings['horizontalSharingTheme'] : '';

        switch ($horizontalThemvalue) {
            case '32':
                $size = '32';
                $interface = 'horizontal';
                break;

            case '16':
                $size = '16';
                $interface = 'horizontal';
                break;

            case 'single_large':
                $size = '32';
                $interface = 'simpleimage';
                break;

            case 'single_small':
                $size = '16';
                $interface = 'simpleimage';
                break;

            case 'counter_vertical':
                $ishorizontal = 'true';
                $interface = 'simple';
                $countertype = 'vertical';
                break;

            case 'counter_horizontal':
                $ishorizontal = 'true';
                $interface = 'simple';
                $countertype = 'horizontal';
                break;

            default:
                $size = '32';
                $interface = 'horizontal';
                break;
        }
        if (!empty($ishorizontal)) {
            $providers = $this->get_counter_providers('horizontal', $loginRadiusSettings);

            // prepare counter script
            $sharingScript .= 'LoginRadius.util.ready( function() { $SC.Providers.Selected = ["' . $providers . '"]; $S = $SC.Interface.' . $interface . '; $S.isHorizontal = ' . $ishorizontal . '; $S.countertype = \'' . $countertype . '\'; $u = LoginRadius.user_settings;  if(typeof document.getElementsByName("viewport")[0] != "undefined"){$u.isMobileFriendly=true;}; $S.show( "loginRadiusHorizontalSharing" ); } );';
        } else {
            $providers = $this->get_sharing_providers('horizontal', $loginRadiusSettings);

            // prepare sharing script
            $sharingScript .= 'LoginRadius.util.ready( function() { $i = $SS.Interface.' . $interface . '; $SS.Providers.Top = ["' . $providers . '"]; $u = LoginRadius.user_settings;';
            if (isset($apikey) && !empty($apikey)) {
                $sharingScript .= '$u.apikey= \'' . trim($apikey) . '\';';
            }
            $sharingScript .= '$i.size = ' . $size . '; $u.sharecounttype="url";  if(typeof document.getElementsByName("viewport")[0] != "undefined"){$u.isMobileFriendly=true;}; $i.show( "loginRadiusHorizontalSharing" ); } );';
        }
        return $sharingScript;
    }

    protected function _construct()
    {
        parent::_construct();
    }
}