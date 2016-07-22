<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Helper_Utils extends Mage_Core_Helper_Abstract {
	public function getTargetAttr($type = '') {
		$attribs = '';
		switch ($type) {
			default :
			case '0' :
			case '' :
				break;
			case '1' :
			case '_blank' :
				$attribs = "target=\"_blank\"";
				break;
			case '2' :
			case '_popup' :
				$attribs = "onclick=\"window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,false');return false;\"";
				break;
		}
		return $attribs;
	}
}
?>