<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Adminhtml_PositionController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('megamenu/position')
			->_addBreadcrumb(
                  Mage::helper('megamenu')->__('Positions'),
                  Mage::helper('megamenu')->__('Positions')
              )
            ->_addBreadcrumb(
                  Mage::helper('megamenu')->__('Manage Positions'),
                  Mage::helper('megamenu')->__('Manage Positions')
              );
		
		return $this;
	}
 
	public function indexAction() {
		$this->_initAction()->renderLayout();
	}

	public function editAction() {

		$this->_title($this->__('Positions'))
			 ->_title($this->__('Manage Position'));
		
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('megamenu/position')->load($id);
		
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('position_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('megamenu/position');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Positions'), Mage::helper('adminhtml')->__('Manage Positions'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Position'), Mage::helper('adminhtml')->__('Position'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('megamenu/adminhtml_position_edit'))
				->_addLeft($this->getLayout()->createBlock('megamenu/adminhtml_position_edit_tabs'));

			$this->renderLayout();
		} else {
			
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenu')->__('Position does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			$model = Mage::getModel('megamenu/position')->setData($data)->setId( $this->getRequest()->getParam('id') );
			try {
				$model->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('megamenu')->__('Position was saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenu')->__('Unable to find position to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				Mage::getModel('megamenu/position')->load($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Position was deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $position_ids = $this->getRequest()->getParam('position_param');
        if( !is_array($position_ids) ) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select position(s)'));
        } else {
            try {
                foreach ($position_ids as $position_id) {
                    Mage::getModel('megamenu/position')->load($position_id)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d position(s) were deleted', count($position_ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStateAction() {
        $position_ids = $this->getRequest()->getParam('position_param');
        if( !is_array($position_ids) ) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select position(s)'));
        } else {
            try {
                foreach ($position_ids as $position_id) {
                    $model = Mage::getSingleton('megamenu/position')
                        ->load($position_id)
                        ->setState( $this->getRequest()->getParam('state') )
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d position(s) were updated', count($position_ids))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction(){
        $content = $this->getLayout()->createBlock('megamenu/adminhtml_position_grid')->getCsv();
        $this->_prepareDownloadResponse('et_megamenu_position.csv', $content);
    }

    public function exportXmlAction(){
        $content = $this->getLayout()->createBlock('megamenu/adminhtml_position_grid')->getXml();
        $this->_prepareDownloadResponse('et_megamenu_positions.xml', $content);
    }
    
    public function importFromCatalogAction(){
    	$errorMessage = Mage::helper('megamenu')->__('Unable to import categories.');
    	try {
    		$positions = Mage::getModel('megamenu/position')
    			->getCollection()
    			->getItems();
    		
    		$categories = Mage::getModel('catalog/category')
    			->getCollection()
    			->addAttributeToSelect('*')
    			->addPathFilter('^1/[0-9/]+')
    			->addOrder('level', 'asc')
    			->addOrder('position', 'asc')
    			->getItems();

    		$root_categories = array();
    		foreach ($categories as $i => $cat){
    			if ( $cat->level == 1 ){
    				$root_categories[$cat->name] = &$categories[$i];
    			}
    		}
    		
    		$current_positions = array();
    		foreach ($positions as $i => $pos){
    			$current_positions[$pos->title] = &$positions[$i];
    		}
    		
    		$add_positions = array_diff_key($root_categories, $current_positions);
    		
    		// var_dump($add_positions, $current_positions, $root_categories);die;
    		
    		if ( count($add_positions) > 0){
    			
    			$cats = array();
    			foreach ($categories as $category) {
    				$c = new stdClass();
    				$c->name  = $category->getName();
    				$c->id    = $category->getId();
    				$c->level = $category->getLevel();
    				$c->parent_id = $category->getParentId();
    				$c->is_active = $category->getIsActive() ? 1 : 0;
    				$cats[$c->id] = $c;
    			}
    			
    			foreach($cats as $id => $c){
    				if (isset($cats[$c->parent_id])){
    					if (!isset($cats[$c->parent_id]->child)){
    						$cats[$c->parent_id]->child = array();
    					}
    					$cats[$c->parent_id]->child[] =& $cats[$id];
    				}
    			}
    			
    			foreach ($add_positions as $ref){
    				
    				$newposition = Mage::getModel('megamenu/position')
    					->setTitle( $ref->name )
    					->setDescription('Synchronize from Catalog Category ID = '.$ref->getId())
    					->setState(1)
    					->save();
    				ob_start();
    				if ( ($position_id = $newposition->getId()) && ($position_root_id = $newposition->getRootId()) ){
    					echo "position_id = $position_id, position_root_id = $position_root_id ".PHP_EOL;
    					$menu_id = array();
    					
    					$cat = &$cats[$ref->getId()];
    					print_r($cat);echo PHP_EOL;
    					// var_dump( $position_id, $position_root_id, $cat, $newposition->getData() ); die;
    					
    					$stack = array();
    					if (isset($cat->child) && count($cat->child)){
    						foreach(array_reverse($cat->child) as $child){
    							array_push($stack, $child);
    						}
    					}
    					print_r($stack);echo PHP_EOL;
    					
    					while( count($stack)>0 ){
    						$cat = array_pop($stack);
    							
    						$newmenu = Mage::getModel('megamenu/menu');
    						$newmenu->setTitle( $cat->name );
    						$newmenu->setState( $cat->is_active ) ;
    						$newmenu->setParentId( !isset($menu_id[$cat->parent_id]) ? $position_root_id : $menu_id[$cat->parent_id] );
    						$newmenu->setPositionId( $position_id );
    						$newmenu->setMenuType( ET_Megamenu_Model_System_Config_Source_MenuType::CATEGORY );
    						$newmenu->setCategoryLink( "category/{$cat->id}" );
    						$newmenu->save();
    						$menu_id[$cat->id] = $newmenu->getId();
    							
    						if (isset($cat->child) && count($cat->child)){
    							foreach(array_reverse($cat->child) as $child){
    								array_push($stack, $child);
    							}
    						}
    					}
    					
    				}
    				
    			}
    			$this->_getSession()->addSuccess( Mage::helper('megamenu')->__('Categories Synchronized.') );
    			
    		} else {
    			$this->_getSession()->addError( Mage::helper('megamenu')->__('Categories already synchronized.') );
    		}
    	} catch (Mage_Core_Exception $e) {
    		$this->_getSession()->addError($errorMessage . ' ' . $e->getMessage());
    	} catch (Exception $e) {
    		$this->_getSession()->addError($errorMessage . ' ' . $e->getMessage());
    		Mage::logException($e);
    	}
    	$this->_redirect('*/*');
    }
    
}