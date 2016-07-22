<?php

class Loginradius_Sociallogin_Model_System_Config_Info extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {

        // Get LoginRadius Module Thanks message container..
        $this->render_module_thanks_message_container();

        // Get LoginRadius Module information container..
        $this->render_module_info_container();

        // Get LoginRadius Module Help & Documentations container..
        $this->render_module_help_and_documentations_container();

        // Get LoginRadius Module Support Us container..
        $this->render_module_support_us_container();

        // Get LoginRadius Module Support Us container..
        $this->render_module_admin_script_container();
    }

    public function render_module_thanks_message_container()
    {
        ?>
        <fieldset class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_left" id="lr_thank_message_container">
            <h4 class="lr_admin_fieldset_title"><strong><?php echo $this->__('Thank you for installing LoginRadius Extension!') ?></strong></h4>

            <p>
                <?php echo $this->__('To activate the extension, you will need to first configure it (manage your desired social networks, etc.) from your LoginRadius account. If you do not have an account, click') ?>
                <a target="_blank" href="http://www.loginradius.com/"><?php echo $this->__('here') ?></a> <?php echo $this->__('and create one for FREE!') ?>
            </p>

            <p>
                <?php echo $this->__('We also offer Social Plugins for') ?>
                <a href="http://ish.re/ADDT" target="_blank">Wordpress</a>,
                <a href="http://ish.re/8PE6" target="_blank">Joomla</a>,
                <a href="http://ish.re/8PE9" target="_blank">Drupal</a>,
                <a href="http://ish.re/8PED" target="_blank">vBulletin</a>,
                <a href="http://ish.re/8PEE" target="_blank">VanillaForum</a>,
                <a href="http://ish.re/8PEG" target="_blank">osCommerce</a>,
                <a href="http://ish.re/8PEH" target="_blank">PrestaShop</a>,
                <a href="http://ish.re/8PFQ" target="_blank">X-Cart</a>,
                <a href="http://ish.re/8PFR" target="_blank">Zen-Cart</a>,
                <a href="http://ish.re/8PFS" target="_blank">DotNetNuke</a>,
                <a href="http://ish.re/8PFT" target="_blank">SMF</a><?php echo $this->__('and') ?>
                <a href="http://ish.re/8PFV" target="_blank">phpBB</a> !
            </p>
            </br>
            <div style="margin-top:10px">
                <a style="text-decoration:none;margin-right:10px;" href="https://www.loginradius.com/" target="_blank">
                    <input class="form-button" type="button" value="<?php echo $this->__('Set up my FREE account!') ?>">
                </a>
                <a class="loginRadiusHow" target="_blank"
                   href="http://ish.re/ATM4">(<?php echo $this->__('How to set up an account?') ?>)</a>
            </div>
        </fieldset>
    <?php
    }

    public function render_module_info_container()
    {
        ?>
        <fieldset class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_right" id="lr_extension_info_container">
            <h4 class="lr_admin_fieldset_title"><strong><?php echo $this->__('Extension Information!') ?></strong></h4>

            <div style="margin:5px 0">
                <strong>Version: </strong>4.1.0 <br/>
                <strong>Author:</strong> LoginRadius<br/>
                <strong>Website:</strong> <a href="https://www.loginradius.com" target="_blank">www.loginradius.com</a>
                <br/>
                <strong>Community:</strong> <a href="http://community.loginradius.com" target="_blank">community.loginradius.com</a>
                <br/>

                <div id="sociallogin_get_update" style="float:left;">To receive updates on new features, releases, etc. Please connect to one of our social media pages
                </div>
                <div id="lr_media_pages_container">
                    <a target="_blank" href="https://www.facebook.com/loginradius"><img
                            src="<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'adminhtml/default/default/Loginradius/Sociallogin/images/media-pages/facebook.png'; ?>"></a>
                    <a target="_blank" href="https://twitter.com/LoginRadius"><img
                            src="<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'adminhtml/default/default/Loginradius/Sociallogin/images/media-pages/twitter.png'; ?>"></a>
                    <a target="_blank" href="https://plus.google.com/+Loginradius"> <img
                            src="<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'adminhtml/default/default/Loginradius/Sociallogin/images/media-pages/google.png'; ?>"></a>
                    <a target="_blank" href="http://www.linkedin.com/company/loginradius"> <img
                            src="<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'adminhtml/default/default/Loginradius/Sociallogin/images/media-pages/linkedin.png'; ?>"></a>
                    <a target="_blank" href="https://www.youtube.com/user/LoginRadius"> <img
                            src="<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'adminhtml/default/default/Loginradius/Sociallogin/images/media-pages/youtube.png'; ?>"></a>
                </div>
            </div>
        </fieldset>
    <?php
    }

    public function render_module_help_and_documentations_container()
    {
        ?>
        <fieldset class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_left" id="lr_extension_help_container">
            <h4 class="lr_admin_fieldset_title"><strong><?php echo $this->__('Help & Documentations') ?></strong></h4>
            <ul style="float:left; margin-right:43px">
                <li><a target="_blank" href="http://ish.re/9WC9">Extension Installation, Configuration and
                        Troubleshooting</a></li>
                <li><a target="_blank" href="http://ish.re/9VBI">How to get LoginRadius API Key &amp; Secret</a></li>
                <li><a target="_blank" href="http://ish.re/9Z34">Magento Multisite Feature</a></li>
                <li><a target="_blank" href="http://ish.re/96M9">LoginRadius Products</a></li>
            </ul>
            <ul style="float:left; margin-right:43px">
                <li><a target="_blank" href="http://ish.re/8PG2">Discussion Forum</a></li>
                <li><a target="_blank" href="http://ish.re/96M7">About LoginRadius</a></li>
                <li><a target="_blank" href="http://ish.re/8PG8">Social Plugins</a></li>
                <li><a target="_blank" href="http://ish.re/6JMW">Social SDKs</a></li>
            </ul>
        </fieldset>
    <?php
    }

    public function render_module_support_us_container()
    {
        ?>
        <fieldset class="lr_admin_configuration_info_fieldsets lr_configuration_info_fieldsets_right" id="lr_extension_support_container">
            <h4 class="lr_admin_fieldset_title"><strong><?php echo $this->__('Support Us') ?></strong></h4>

            <p>
                <?php echo $this->__('If you liked our FREE open-source extension, please send your feedback/testimonial to') ?>
                <a href="mailto:feedback@loginradius.com">feedback@loginradius.com</a> !</p>
        </fieldset>
        <div style='clear:both'></div>
        <div id="loginRadiusKeySecretNotification" style="background-color: rgb(255, 255, 224); border: 1px solid rgb(230, 219, 85); padding: 5px; margin-bottom: 11px; display:none"><?php echo $this->__('To activate the <strong>Social Login</strong>, insert LoginRadius API Key and Secret in the <strong>Social Login Basic Settings</strong> section below. <strong>Social Sharing does not require API Key and Secret</strong>') ?>
        </div>
        <div style='clear:both'></div>
    <?php
    }

    public function render_module_admin_script_container()
    {
        ?>
        <script type="text/javascript">var islrsharing = true;
            var islrsocialcounter = true;</script>
        <script type="text/javascript" src='//share.loginradius.com/Content/js/LoginRadius.js'></script>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                if (jQuery('#sociallogin_options_apisettings_apikey').val() == "" || jQuery('#sociallogin_options_apisettings_apisecret').val() == "") {
                    jQuery('#loginRadiusKeySecretNotification').style.display = 'block';
                }
                var sharingType = ['horizontal', 'vertical'];
                var sharingModes = ['Sharing', 'Counter'];
                for (var i = 0; i < sharingType.length; i++) {
                    for (var j = 0; j < sharingModes.length; j++) {
                        if (sharingModes[j] == 'Counter') {
                            var providers = $SC.Providers.All;
                        } else {
                            var providers = $SS.Providers.More;
                        }
                        //populate sharing providers checkbox
                        loginRadiusCounterHtml = "<ul class='checkboxes'>";
                        // prepare HTML to be shown as Vertical Counter Providers
                        for (var ii = 0; ii < providers.length; ii++) {
                            loginRadiusCounterHtml += '<li><input type="checkbox" id="' + sharingType[i] + '_' + sharingModes[j] + '_' + providers[ii] + '" ';
                            loginRadiusCounterHtml += 'value="' + providers[ii] + '"> <label for="' + sharingType[i] + '_' + sharingModes[j] + '_' + providers[ii] + '">' + providers[ii] + '</label></li>';
                        }
                        loginRadiusCounterHtml += "</ul>";
                        var tds = document.getElementById('row_sociallogin_options_' + sharingType[i] + 'Sharing_' + sharingType[i] + sharingModes[j] + 'Providers').getElementsByTagName('td');
                        tds[1].innerHTML = loginRadiusCounterHtml;
                    }
                    jQuery('#row_sociallogin_options_' + sharingType[i] + 'Sharing_' + sharingType[i] + 'CounterProvidersHidden').hide();
                }
                loginRadiusPrepareAdminUI();
            });
            // toggle sharing/counter providers according to the theme and sharing type
            function loginRadiusToggleSharingProviders(element, sharingType) {
                if (element.value == '32' || element.value == '16') {
                    document.getElementById('row_sociallogin_options_' + sharingType + 'Sharing_' + sharingType + 'SharingProviders').style.display = 'table-row';
                    document.getElementById('row_sociallogin_options_' + sharingType + 'Sharing_' + sharingType + 'CounterProviders').style.display = 'none';
                    document.getElementById('row_sociallogin_options_' + sharingType + 'Sharing_' + sharingType + 'SharingProvidersHidden').style.display = 'table-row';
                } else if (element.value == 'single_large' || element.value == 'single_small') {
                    document.getElementById('row_sociallogin_options_' + sharingType + 'Sharing_' + sharingType + 'SharingProviders').style.display = 'none';
                    document.getElementById('row_sociallogin_options_' + sharingType + 'Sharing_' + sharingType + 'CounterProviders').style.display = 'none';
                    document.getElementById('row_sociallogin_options_' + sharingType + 'Sharing_' + sharingType + 'SharingProvidersHidden').style.display = 'none';
                } else {
                    document.getElementById('row_sociallogin_options_' + sharingType + 'Sharing_' + sharingType + 'SharingProviders').style.display = 'none';
                    document.getElementById('row_sociallogin_options_' + sharingType + 'Sharing_' + sharingType + 'CounterProviders').style.display = 'table-row';
                    document.getElementById('row_sociallogin_options_' + sharingType + 'Sharing_' + sharingType + 'SharingProvidersHidden').style.display = 'none';
                }
            }
        </script>
    <?php
    }

}
