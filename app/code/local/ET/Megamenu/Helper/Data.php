<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Helper_Data extends Mage_Core_Helper_Data {
	public function getParents($params = array()) {
		$options [] = array (
				'value' => '',
				'label' => Mage::helper('megamenu')->__( '--Please Select--' )
		);
		$position_id = isset ( $params ['position_id'] ) ? $params ['position_id'] : 0;
		if ($position_id) {
			$exclude_id = isset ( $params ['exclude_id'] ) ? $params ['exclude_id'] : 0;
			$menu = Mage::getModel ( 'megamenu/menu' );
			if ($exclude_id) {
				$menu->load ( $exclude_id );
				$parents = $menu->mayBeParents ();
			} else {
				$menu->setPositionId ( $position_id );
				$menu_root_id = $menu->getRootId ();
				$menu->load ( $menu_root_id );
				$parents = $menu->getTree ();
			}
			
			if ($parents->getSize ()) {
				$_options = array ();
				$prefix = isset ( $params ['addprefix'] ) ? '--  ' : "";
				foreach ( $parents->getItems () as $menu ) {
					$_options [] = array (
							'value' => $menu->getId (),
							'label' => ($menu->getDepth () ? str_repeat ( $prefix, $menu->getDepth () ) : '') . $menu->getTitle ()
					);
				}
				$options = $_options;
			}
		}
		return $options;
	}
	public function getChildren($params = array()) {
		$options = array ();
		
		$parent_id = isset ( $params ['parent_id'] ) ? $params ['parent_id'] : 0;
		if ($parent_id) {
			$exclude_id = isset ( $params ['exclude_id'] ) ? $params ['exclude_id'] : 0;
			$parent = Mage::getModel ( 'megamenu/menu' )->load ( $parent_id );
			$children = $parent->getOrdering ();
			
			if ($children->getSize ()) {
				$options [] = array (
						'value' => 'first-child',
						'label' => 'First'
				);
				foreach ( $children->getItems () as $menu ) {
					if ($exclude_id == $menu->getId ())
						continue;
					$options [] = array (
							'value' => $menu->getId (),
							'label' => $menu->getTitle ()
					);
				}
			}
		}
		$options [] = array (
				'value' => 'last-child',
				'label' => 'Last'
		);
		return $options;
	}
}