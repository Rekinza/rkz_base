<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Adminhtml_MenuController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('et/megamenu/menu')
			->_addBreadcrumb(
                  Mage::helper('megamenu')->__('Menus'),
                  Mage::helper('megamenu')->__('Menus')
              )
            ->_addBreadcrumb(
                  Mage::helper('megamenu')->__('Manage Menus'),
                  Mage::helper('megamenu')->__('Manage Menus')
              );
		
		return $this;
	}
 
	public function indexAction() {
		$this->_initAction()->renderLayout();
	}

	public function editAction() {
		
		$this->_title($this->__('Menus'))
			 ->_title($this->__('Manage Menu'));
		
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('megamenu/menu')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if ( !empty($data) ) {
				$model->setData($data);
			}

			Mage::register('menu_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('megamenu/menu');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Menu'), Mage::helper('adminhtml')->__('Manage Menu'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu'), Mage::helper('adminhtml')->__('Menu'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('megamenu/adminhtml_menu_edit'))
				->_addLeft($this->getLayout()->createBlock('megamenu/adminhtml_menu_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenu')->__('Menu does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		
		if ( ($post_data = $this->getRequest()->getPost()) && count($post_data)>3 ) {
	  		$post_data['name'] = Mage::helper('megamenu')->stripTags($post_data['name']);
			// $post_data['description'] = Mage::helper('megamenu')->stripTags($post_data['description']);
	  		
			$model = Mage::getModel('megamenu/menu')->setData($post_data)->setId( $this->getRequest()->getParam('id') );
			try {
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('megamenu')->__('Menu was saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($post_data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        // Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenu')->__('Unable to find menu to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				Mage::getModel('megamenu/menu')->load($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Menu was deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $menu_ids = $this->getRequest()->getParam('menu_param');
        if(!is_array($menu_ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($menu_ids as $menu_id) {
                    Mage::getModel('megamenu/menu')->load($menu_id)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were deleted', count($menu_ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStateAction(){
        $menu_ids = $this->getRequest()->getParam('menu_param');
        if(!is_array($menu_ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select menu(s)'));
        } else {
            try {
            	$new_state = $this->getRequest()->getParam('state');
                foreach ($menu_ids as $menu_id) {
                    Mage::getSingleton('megamenu/menu')
                        ->load($menu_id)
                        ->setState( $new_state )
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d menu(s) were updated', count($menu_ids))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction(){
        $fileName   = 'et_megamenu_menu.csv';
        $content    = $this->getLayout()->createBlock('megamenu/adminhtml_menu_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportXmlAction(){
        $fileName   = 'et_megamenu_menu.xml';
        $content    = $this->getLayout()->createBlock('megamenu/adminhtml_menu_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}