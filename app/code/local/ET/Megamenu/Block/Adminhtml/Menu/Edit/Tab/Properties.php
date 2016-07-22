<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Block_Adminhtml_Menu_Edit_Tab_Properties extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface{

	protected $_defaultElementType = 'text';
	protected $_currentMenu = null;
    
	protected function _prepareForm(){
		
		if (Mage::getSingleton('adminhtml/session')->getMenuData()) {
			$current = Mage::getSingleton('adminhtml/session')->getMenuData();
			Mage::getSingleton('adminhtml/session')->getMenuData(null);
		} elseif (Mage::registry('menu_data')) {
			$current = Mage::registry('menu_data');
		}
		
		$is_edit_mode = !(! $current->getId());
		$is_root_menu = $current->getParentId() == 0 && $current->getId() > 0;
		
		if ($is_edit_mode) {
			$this->_currentMenu = $current;
			$children = Mage::helper('megamenu')->getChildren(array('parent_id' => $current->getParentId()));
			$ordering = 'last-child';
			$matched = false;
			foreach ($children as $k => $child) {
				if ($matched){
					// set is next value
					$ordering = $child['value'];
					break;
				}
				if ($current->getId() == $child['value']) {
					$matched = true;
					unset($children[$k]);
				}
			}
			$current->setOrdering($ordering);
		} else{
			// TODO: set default values for new menu item
			$current->setOrdering('last-child');
			$current->setState(ET_Megamenu_Model_System_Config_Source_State::STATE_PUBLISHED);
		}
		
		$form = new Varien_Data_Form();
		$fieldset = $form->addFieldset('menu_form', array('legend'=>Mage::helper('megamenu')->__('Menu Attributes')));

		$state_field = $fieldset->addField('menu_form_state', 'select', array(
				'label'     => Mage::helper('megamenu')->__('State'),
				'name'      => 'state',
				'value' 	=> $current->getData('state'),
				'values'    => Mage::getModel('megamenu/system_config_source_state')->toOptionArray()
		));
		
		$title_field = $fieldset->addField('menu_form_title', 'text', array(
			'label'     => Mage::helper('megamenu')->__('Title'),
			'name'      => 'title',
			'class'     => 'required-entry',
			'required'  => true,
			'value' => $current->getData('title'),
		    'depends' => array(
		            'menu_form_state' => 0
		        )
		));
		
		$subtitle_field = $fieldset->addField('menu_form_subtitle', 'text', array(
				'label'     => Mage::helper('megamenu')->__('Subtitle'),
				'name'      => 'subtitle',
				'value' => $current->getData('subtitle')
		));
		
		$position_field = $fieldset->addField('menu_form_position_id', $is_edit_mode ? 'hidden' : 'select', array(
			'label'     => Mage::helper('megamenu')->__('Position'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'position_id',
			'values'    => Mage::getModel('megamenu/system_config_source_positions')->toOptionArray(),
			'value' => $current->getData('position_id')
		));
		$position_field_id = $position_field->getId();
				
		$parent_field = $fieldset->addField('menu_form_parent_id', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Parent'),
			'name'      => 'parent_id',
			'values'    => $is_edit_mode ? $this->_loadParents( $current->getPositionId(), $current->getId() ): $this->_loadParents(),
			'onchange'	=> 'onParentChange(this)',
			'value'     => $current->getData('parent_id')
		));
		
		$parent_field_id = $parent_field->getId();
		
		$position_field->setOnchange( !$is_edit_mode ? 'onPositionChange(this)' : '' );
		
		if( $is_edit_mode ){
			// $position_field->setDisabled('disabled');
			$position_field->setAfterElementHtml('
				<script>
					$$("select#'.$position_field_id.' option").each(function(element){
						if ( !element.selected ){
							element.disabled = true;
						}
					});
				</script>');
		} else{
			$position_field->setAfterElementHtml('
				<script>
					function onPositionChange( element ) {
						var position0 = element.value;
						return load_parents(position0, "'.$parent_field_id.'");
					}
				</script>');
		}
		
		$order_field = $fieldset->addField('menu_form_ordering', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Ordering'),
			'name'      => 'ordering',
			'values'    => $is_edit_mode ? $this->_loadChildren( $current->getParentId(), $current->getId() ) : $this->_loadChildren(),
			'value' => $current->getData('ordering')
		));
		$order_field_id = $order_field->getId();
		
		$parent_field ->setAfterElementHtml('<script>
					function onParentChange(element){
						var parent0 = element.value;
						return load_orders(parent0, "'.$order_field_id.'");
					}
				</script>');
		
		$menu_type_field =	$fieldset->addField('menu_form_menu_type', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Type'),
			'name'      => 'menu_type',
			'class'     => 'required-entry',
			'values'    => Mage::getModel('megamenu/system_config_source_menuType')->toOptionArray(),
			'onchange'	=> 'onMenuTypeChange(this)',
			'value'     => $current->getData('menu_type')
		));
		
 		$widgetHelper = $this->getLayout()->createBlock('megamenu/adminhtml_widget_helper');
 		$widgetHelper->init($fieldset, $form);
 		$widgetHelper->setWidgetValues($current->getData());
 		
 		$parameters = array();
 		$parameters[] = new Varien_Object(array(
 				'visible' => 1,
 				'label'     => 'Product',
 				'type'      => 'label',
 				'class'     => 'menu_product_link',
 				'sort_order'=> 20,
 				'key'       => 'product_link',
 				'helper_block' => new Varien_Object(array(
 						'type' => 'adminhtml/catalog_product_widget_chooser',
 				))
 		));
 		
 		$parameters[] = new Varien_Object(array(
 				'visible' => 1,
 				'label'     => 'Category',
 				'type'      => 'label',
 				'class'     => 'menu_category_link',
 				'sort_order'=> 20,
 				'key'       => 'category_link',
 				'helper_block' => new Varien_Object(array(
 					'type' => 'adminhtml/catalog_category_widget_chooser'
 				))
 		));
 		
 		$parameters[] = new Varien_Object(array(
 				'visible' => 1,
 				'label'     => 'CMS Page',
 				'type'      => 'label',
 				'class'     => 'menu_cmspage_link',
 				'sort_order'=> 20,
 				'key'       => 'cmspage_link',
 				'helper_block' => new Varien_Object(array(
 						'type' => 'adminhtml/cms_page_widget_chooser',
 				))
 		));
 		
 		$widgetHelper->setParameters($parameters);
 		$widgetHelper->addFields();
 		
 		$external_link_field = $fieldset->addField('menu_form_external_link', 'text', array(
 				'label'     => Mage::helper('megamenu')->__('External Link'),
 				'name'      => 'external_link',
 				'value' => $current->getData('external_link')
 		));
 		
//  		$magento_route_field = $fieldset->addField('menu_form_magento_route', 'select', array(
//  				'label'     => Mage::helper('megamenu')->__('Magento Router'),
//  				'name'      => 'magento_route',
//  				'value' => $current->getData('magento_route'),
//  				'values'=> Mage::getModel('megamenu/system_config_source_magentoRouters')->toOptionArray()
//  		));
		$fieldset->addField('menu_form_dropuse_description', 'select', array(
			'name' => 'dropdown_content',
			'label' => Mage::helper('megamenu')->__( 'Use' ),
 			'value' => $current->getData('dropdown_content'),
			'values' => Mage::getModel('megamenu/system_config_source_handlers')->toOptionArray()
		));
 		
 		$fieldset->addField ( 'menu_form_description', 'textarea', array (
 				'name' => 'description',
 				'label' => Mage::helper('megamenu')->__( 'Content' ),
 				'title' => Mage::helper('megamenu')->__( 'Content' ),
 				'value' => $current->getData('description')
 		));
 		
//  		$fieldset->addField('menu_form_target', 'select', array(
//  				'label'     => Mage::helper('megamenu')->__('Link Target'),
//  				'name'      => 'target',
//  				'value'     => $current->getData('target'),
//  				'values'	=> Mage::getModel('megamenu/system_config_source_targets')->toOptionArray()
//  		));
 		
//  		$fieldset->addField('menu_form_html_id', 'text', array(
//  				'label'     => Mage::helper('megamenu')->__('ID Attribute'),
//  				'name'      => 'html_id',
//  				'value' => $current->getData('html_id')
//  		));
 		
//  		$fieldset->addField('menu_form_html_class', 'text', array(
//  				'label'     => Mage::helper('megamenu')->__('Class Attribute'),
//  				'name'      => 'html_class',
//  				'value' => $current->getData('html_class')
//  		));
 		
//  		$fieldset->addField('menu_form_class_icon', 'text', array(
//  				'label'     => Mage::helper('megamenu')->__('Class for Icon'),
//  				'name'      => 'class_icon',
//  				'value' => $current->getData('class_icon')
//  		));
 		
//  		$fieldset->addField('menu_form_align', 'select', array(
//  				'label'     => Mage::helper('megamenu')->__('Align'),
//  				'name'      => 'align',
//  				'value'     => $current->getData('align'),
//  				'values'	=> Mage::getModel('megamenu/system_config_source_align')->toOptionArray()
//  		));
 		
 		$fieldset->addField('menu_form_menu_size', 'select', array(
 				'label'     => Mage::helper('megamenu')->__('Size'),
 				'name'      => 'menu_size',
 				'value' => $current->getData('menu_size'),
 				'values' => Mage::getModel('megamenu/system_config_source_size')->toOptionArray()
 		));
 		
 		$fieldset->addField('menu_form_dropdown_option_comments', 'label', array(
 				'label'     => Mage::helper('megamenu')->__('Dropdown options'),
 		))
 		->setRenderer( $this->getLayout()->createBlock('adminhtml/system_config_form_field_heading') );
 		
//  		$fieldset->addField('menu_form_dropdown_hover', 'select', array(
//  				'label'     => Mage::helper('megamenu')->__('Event'),
//  				'name'      => 'dropdown_hover',
//  				'value' => $current->getData('dropdown_hover'),
//  				'values' => Mage::getModel('megamenu/system_config_source_event')->toOptionArray()
//  		));
 		
 		$fieldset->addField('menu_form_dropdown_layout', 'select', array(
 				'label'     => Mage::helper('megamenu')->__('Dropdown Style'),
 				'name'      => 'dropdown_layout',
 				'value' => $current->getData('dropdown_layout'),
 				'values' => Mage::getModel('megamenu/system_config_source_dropdownStyle')->toOptionArray()
 		));
 		

 		$fieldset->addField('menu_form_dropdown_size', 'text', array(
 				'label'     => Mage::helper('megamenu')->__('Dropdown Size'),
 				'name'      => 'dropdown_size',
 				'value' => $current->getData('dropdown_size')
 		));
//  		$fieldset->addField('menu_form_dropdown_size', 'select', array(
//  				'label'     => Mage::helper('megamenu')->__('Dropdown Size'),
//  				'name'      => 'dropdown_size',
//  				'value' => $current->getData('dropdown_size'),
//  				'values' => Mage::getModel('megamenu/system_config_source_size')->toOptionArray(),
//  				'depends' => array(
//  					'dropdown_layout' => ET_Megamenu_Model_System_Config_Source_DropdownStyle::GRID
//  				)
//  		));
 		
 		
		//end process textarea content

		$menu_type_field->setAfterElementHtml("
<script type='text/javascript'>
//<![CDATA[
	var depends_menu_types = {rules: []};
	var hide_me = function(e){ if (e) e.hide() }, show_me = function(e){ if (e) e.show() };
	function onMenuTypeChange(element){
		var type = element.value;
		if ( depends_menu_types.rules.length ){
			depends_menu_types.rules.each(function(rule){
				if ( type == rule.value ){
					rule.elements.each(show_me);
				} else {
					rule.elements.each(hide_me);
				}
			});
		}
	}
	
	(function() {
		var instantiateRules = function(){
			/* external_link rules */
			var external_link_rules = { value: ".ET_Megamenu_Model_System_Config_Source_MenuType::EXTERNAL.", elements: [] };
			$$('label[for$=\"external_link\"]').each(function(el){
				var td = el.up();
				if (td.className=='label'){
					var tr = td.up();
					if (tr.nodeName.toLowerCase()=='tr'){
						external_link_rules.elements.push(tr);
					}
				}
			});
			if (external_link_rules.elements.length){
				depends_menu_types.rules.push(external_link_rules);
			}
					
			/* magento_route rules */
			var magento_route_rules = { value: ".ET_Megamenu_Model_System_Config_Source_MenuType::ROUTER.", elements: [] };
			$$('label[for$=\"magento_route\"]').each(function(el){
				var td = el.up();
				if (td.className=='label'){
					var tr = td.up();
					if (tr.nodeName.toLowerCase()=='tr'){
						magento_route_rules.elements.push(tr);
					}
				}
			});
			if (magento_route_rules.elements.length){
				depends_menu_types.rules.push(magento_route_rules);
			}
					
			/* content rules */
			var content_rules = { value: ".ET_Megamenu_Model_System_Config_Source_MenuType::CONTENT.", elements: [] };
			$$('label[for$=\"description\"]').each(function(el){
				var td = el.up();
				if (td.className=='label'){
					var tr = td.up();
					if (tr.nodeName.toLowerCase()=='tr'){
						content_rules.elements.push(tr);
					}
				}
			});
			if (content_rules.elements.length){
				depends_menu_types.rules.push(content_rules);
			}
				
			/* product_link rules */
			var product_link_rules = { value: ".ET_Megamenu_Model_System_Config_Source_MenuType::PRODUCT.", elements: [] };
			$$('label[for$=\"product_link\"]').each(function(el){
				var td = el.up();
				if (td.className=='label'){
					var tr = td.up();
					if (tr.nodeName.toLowerCase()=='tr'){
						product_link_rules.elements.push(tr);
					}
				}
			});
			if (product_link_rules.elements.length){
				depends_menu_types.rules.push(product_link_rules);
			}
				
			/* category_link rules */
			var category_link_rules = { value: ".ET_Megamenu_Model_System_Config_Source_MenuType::CATEGORY.", elements: [] };
			$$('label[for$=\"category_link\"]').each(function(el){
				var td = el.up();
				if (td.className=='label'){
					var tr = td.up();
					if (tr.nodeName.toLowerCase()=='tr'){
						category_link_rules.elements.push(tr);
					}
				}
			});
			if (category_link_rules.elements.length){
				depends_menu_types.rules.push(category_link_rules);
			}
				
			/* cmspage_link rules */
			var cmspage_link_rules = { value: ".ET_Megamenu_Model_System_Config_Source_MenuType::PAGE.", elements: [] };
			$$('label[for$=\"cmspage_link\"]').each(function(el){
				var td = el.up();
				if (td.className=='label'){
					var tr = td.up();
					if (tr.nodeName.toLowerCase()=='tr'){
						cmspage_link_rules.elements.push(tr);
					}
				}
			});
			if (cmspage_link_rules.elements.length){
				depends_menu_types.rules.push(cmspage_link_rules);
			}
			if ( $('menu_form_menu_type') ){
				onMenuTypeChange( $('menu_form_menu_type') )
			}
		}
		if (document.loaded) { //allow load over ajax
			instantiateRules();
		} else {
			document.observe(\"dom:loaded\", instantiateRules);
		}
	})();
//]]>
</script>");

		$this->setForm($form);
		return parent::_prepareForm();
	}
	
	protected function _loadParents( $position_id = null, $exclude_id = null ){
		return Mage::helper('megamenu')->getParents( array('position_id' => $position_id, 'exclude_id' => $exclude_id, 'addprefix' => true) );
	}
	protected function _loadChildren( $parent_id = null, $exclude_id = null ){
		return Mage::helper('megamenu')->getChildren( array('parent_id' => $parent_id, 'exclude_id' => $exclude_id) );
	}
	
	public function _toHtml(){

        // Get the default HTML for this option
        $html = parent::_toHtml();
		
		//  tam thoi khoa' objTreeitems khi edit item,
		// if(!$modelMenuitems){
		
		$html = '
	<script type="text/javascript">
		if(typeof objTreeitems=="undefined") {
			var objTreeitems = {};
		}
		var objTreeitems=Class.create();
		objTreeitems.prototype=	{
			
			initialize: function(){
				this.option_tpl=\'<option value="#{id}">#{title}</option>\';
				this.listObjects = [];
				this.allowDisabled = 1;
				this.allowEnabled = 0;
			},
			
			loadMenus: function(url, position_id, callback){
				new Ajax.Request(url,{encoding:"UTF-8", method:"post",
					parameters:{
						position_id: position_id
						, addprefix:true
					},
					onSuccess: function(resp){
						resp = resp.responseText.evalJSON();
						callback(resp);
					},
					onLoading : function(){
						$("loading-mask").show();
					},
					onFailure : function(resp){
					},
					onComplete: function(){
						$("loading-mask").hide();
					}
				});
			},
			getOptions: function(tpl, list_opts){
				var ops_html = "";
				if ( list_opts.length ){
					var option_tpl = new Template(tpl);
					for(var i=0; i< list_opts.length; i++){
						ops_html += option_tpl.evaluate( list_opts[i] );
					}
				}
				return ops_html;
			}
		}
				
		// var position_menus = new objTreeitems();
		// var menu_orders = new objTreeitems();
		
		var position_parents = {};
		var menu_orders = {};
		var loading = false;
		var load_parents = function(position_id, parent_field_id){
			if ( loading ) return false;
			var js_position_id = "position_" + position_id;
			if ( typeof(position_parents[js_position_id]) == "undefined" ){
				position_parents[js_position_id] = [];
				new Ajax.Request("'.Mage::getUrl('megamenu/index/ajaxGetParents').'",{encoding:"UTF-8", method:"post",
					parameters:{
						position_id: position_id,
						' . ($this->_currentMenu ? 'exclude_id: ' . $this->_currentMenu->getId() .',' : '' ) . '
						addprefix: true
					},
					onSuccess: function(resp){
						resp = resp.responseText.evalJSON();
						var items = resp["items"];
						if ( items.length ){
							for( var i = 0; i < items.length; i++ ){
								position_parents[js_position_id].push(items[i]);
							}
						}
					},
					onLoading : function(){
						loading = true;
						$("loading-mask").show();
					},
					onFailure : function(resp){
					},
					onComplete: function(){
						$("loading-mask").hide();
						loading = false;
						update_parents(position_id, parent_field_id);
					}
				});
			} else {
				update_parents(position_id, parent_field_id);
			}
		};
		var update_parents = function( position_id, parent_field_id ){
			var js_position_id = "position_" + position_id;
			var ops_html = "", opts = position_parents[js_position_id];
			if ( opts.length ){
				var option_tpl = new Template("<option value=\"#{value}\">#{label}</option>");
				for(var i=0; i< opts.length; i++){
					ops_html += option_tpl.evaluate( opts[i] );
				}
				$(parent_field_id).update(ops_html);
				if (onParentChange){
					onParentChange( $(parent_field_id) );
				}
				return true;
			}
			return false;
		}
		var load_orders = function( parent_id, order_field_id ){
			if ( loading ) return false;
			var js_parent_id = "parent_" + parent_id;
			if ( typeof(menu_orders[js_parent_id]) == "undefined" ){
				menu_orders[js_parent_id] = [];
				new Ajax.Request("'.Mage::getUrl('megamenu/index/ajaxGetChildren').'",{encoding:"UTF-8", method:"post",
					parameters:{
						parent_id: parent_id,
						' . ($this->_currentMenu ? 'exclude_id: ' . $this->_currentMenu->getId() : '' ) . '
					},
					onSuccess: function(resp){
						resp = resp.responseText.evalJSON();
						var items = resp["items"];
						if ( items.length ){
							for( var i = 0; i < items.length; i++ ){
								menu_orders[js_parent_id].push(items[i]);
							}
						}
					},
					onLoading : function(){
						loading = true;
						$("loading-mask").show();
					},
					onFailure : function(resp){
					},
					onComplete: function(){
						$("loading-mask").hide();
						loading = false;
						update_orders(parent_id, order_field_id);
					}
				});
			} else {
				update_orders(parent_id, order_field_id);
			}
		};
		var update_orders = function( parent_id, order_field_id ){
			var js_parent_id = "parent_" + parent_id;
			var ops_html = "", opts = menu_orders[js_parent_id];
			if ( opts.length ){
				var option_tpl = new Template("<option value=\"#{value}\">#{label}</option>");
				for(var i=0; i< opts.length; i++){
					ops_html += option_tpl.evaluate( opts[i] );
				}
				$(order_field_id).update(ops_html);
				return true;
			}
			return false;
		}
	</script>'
			.$html	;
		// }
		return $html;
	}
 
	/**
	 * Prepare label for tab
	 *
	 * @return string
	 */
	public function getTabLabel() {
		return Mage::helper('cms')->__('Properties');
	}
	
	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle() {
		return Mage::helper('cms')->__('Properties');
	}
	
	/**
	 * Returns status flag about this tab can be shown or not
	 *
	 * @return true
	 */
	public function canShowTab() {
		return true;
	}
	
	/**
	 * Returns status flag about this tab hidden or not
	 *
	 * @return true
	 */
	public function isHidden() {
		return false;
	}
	
	/**
	 * Check permission for passed action
	 *
	 * @param string $action
	 * @return bool
	 */
	protected function _isAllowedAction($action) {
		return Mage::getSingleton('admin/session')->isAllowed('megamenu/menu/' . $action);
	}
}