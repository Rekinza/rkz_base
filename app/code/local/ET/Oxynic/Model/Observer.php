<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Model_Observer {
	
	/**
	 * LESS Compiler
	 * Event: controller_action_layout_render_before
	 * @param Varien_Event_Observer $observer
	 */
	public function onBeforeLayoutRender( $observer ){
		$helper = Mage::helper('oxynic');
		
		// less compile
		$is_enable = (int)$helper->getConfig('live_less_compile', 0);
		$is_admin = Mage::app()->getStore()->isAdmin();
        $is_ajax = Mage::app()->getFrontController()->getRequest()->isAjax();
        $lib_installed = class_exists('Less_Parser');
        
		if ($is_enable && $lib_installed && !$is_admin && !$is_ajax){
			
			$theme_color = $helper->getConfig('theme_color', 'default'); // should be css_name insted of theme_color
			$theme_responsive = $helper->getConfig('theme_responsive', 1);
			
			$ds = Mage::getDesign();
			$skin_base_dir = $ds->getSkinBaseDir();
			$skin_base_url = $ds->getSkinUrl();
			
			$options = array('compress' => 0);
			
			// get less app
			$less_app = $this->getFilename('less/app.less');
			
			if (file_exists($less_app)) {
			    // output to current theme
			    $output_css_suffix = $helper->getCssSuffix($theme_color, $theme_responsive);
				$output_cssf = $skin_base_dir."/css/app-{$theme_color}.css";

				$parser = new Less_Parser($options);
				$parser->SetImportDirs(array(
					$skin_base_dir.'/less/app/'          => $skin_base_url.'/less/app/',
					$skin_base_dir.'/less/bootstrap/'    => $skin_base_url.'/less/bootstrap/',
					$skin_base_dir.'/less/font-awesome/' => $skin_base_url.'/less/font-awesome/'
				));
				$parser->parseFile($less_app, $skin_base_url.'css/');
				
				$less_app_vars = $this->getFilename("less/app-$theme_color.less");
				if (file_exists($less_app_vars)) {
					$parser->parseFile($less_app_vars, $skin_base_url.'css/');
				}
				
				if ($theme_responsive){
				    $responsive_less = $this->getFilename('less/app/responsive.less');
				    if (file_exists($responsive_less)) {
				        $parser->parseFile($responsive_less, $skin_base_url.'css/');
				    }
				} else {
				    $no_responsive_less = $this->getFilename('less/app/no_responsive.less');
				    if (file_exists($no_responsive_less)) {
				        $parser->parseFile($no_responsive_less, $skin_base_url.'css/');
				    }
				}
					
				file_put_contents($output_cssf, $parser->getCss());
			}
		}
	    
	}
	
	public function getFilename($file){
	    return Mage::getDesign()->getFilename($file, array('_type'=>'skin'));
	}
}