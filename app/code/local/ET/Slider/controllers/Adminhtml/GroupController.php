<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Adminhtml_GroupController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('et/slider/group')
			->_addBreadcrumb(
				Mage::helper('slider')->__('Groups'),
				Mage::helper('slider')->__('Groups') )
			->_addBreadcrumb(
				Mage::helper('slider')->__('Manage Groups'),
				Mage::helper('slider')->__('Manage Groups') );
		return $this;
	}
	public function indexAction() {
		$this->_initAction()->renderLayout();
	}
	public function editAction() {
		$this->_title($this->__('Groups'))
			 ->_title($this->__('Manage Groups'));
		
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('slider/group')->load($id);
		
		if($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if( !empty($data) ) {
				$model->setData($data);
			}
			
			Mage::register('group_data', $model);
			
			$this->loadLayout();
			$this->_setActiveMenu('et/slider/group');
			
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Groups'), Mage::helper('adminhtml')->__('Manage Groups'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Group'), Mage::helper('adminhtml')->__('Group'));
			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			
			$this->_addContent($this->getLayout()->createBlock('slider/adminhtml_group_edit'))
				->_addLeft($this->getLayout()->createBlock('slider/adminhtml_group_edit_tabs'));
			
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('slider')->__('Group does not exist'));
			$this->_redirect('*/*/');
		}
	}
	public function newAction() {
		$this->_forward('edit');
	}
	public function saveAction() {
		if($data = $this->getRequest()->getPost()) {
			
			if(isset( $_FILES ['filename'] ['name'] ) && $_FILES ['filename'] ['name'] != '') {
				try {
					/* Starting upload */
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
					$uploader->setAllowedExtensions( array(
							'jpg',
							'jpeg',
							'gif',
							'png'
					) );
					$uploader->setAllowRenameFiles( false );
					
					// Set the file upload mode
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders
					//(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion( false );
					
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS;
					$uploader->save( $path, $_FILES ['filename'] ['name'] );
				} catch( Exception $e ) {
				}
				
				// this way the name is saved in DB
				$data ['filename'] = $_FILES ['filename'] ['name'];
			}
			
			try {
				$model = Mage::getModel('slider/group');
				$model->setData( $data )->setId( $this->getRequest()->getParam('id') );
				$model->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper('slider')->__('Item was successfully saved') );
				Mage::getSingleton('adminhtml/session')->setFormData( false );
				
				if($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array(
							'id' => $model->getId()
					) );
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch( Exception $e ) {
				Mage::getSingleton('adminhtml/session')->addError( $e->getMessage() );
				Mage::getSingleton('adminhtml/session')->setFormData( $data );
				$this->_redirect('*/*/edit', array(
						'id' => $this->getRequest()->getParam('id')
				) );
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError( Mage::helper('slider')->__('Unable to find group to save') );
		$this->_redirect('*/*/');
	}
	public function deleteAction() {
		if($this->getRequest()->getParam('id') > 0) {
			try {
				Mage::getModel('slider/group')->load( $this->getRequest()->getParam('id') )->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper('adminhtml')->__('Group was deleted') );
				$this->_redirect('*/*/');
			} catch( Exception $e ) {
				Mage::getSingleton('adminhtml/session')->addError( $e->getMessage() );
				$this->_redirect('*/*/edit', array(
						'id' => $this->getRequest()->getParam('id')
				) );
			}
		}
		$this->_redirect('*/*/');
	}
	public function massDeleteAction() {
		$group_ids = $this->getRequest()->getParam('group_param');
		if(! is_array( $group_ids )) {
			Mage::getSingleton('adminhtml/session')->addError( Mage::helper('adminhtml')->__('Please select group(s)') );
		} else {
			try {
				foreach( $group_ids as $group_id ) {
					Mage::getModel('slider/group')->load( $group_id )->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper('adminhtml')->__('Total of %d group(s) were deleted', count( $group_ids ) ) );
			} catch( Exception $e ) {
				Mage::getSingleton('adminhtml/session')->addError( $e->getMessage() );
			}
		}
		$this->_redirect('*/*/index');
	}
	public function massStateAction() {
		$group_ids = $this->getRequest()->getParam('group_param');
		if(! is_array( $group_ids )) {
			Mage::getSingleton('adminhtml/session')->addError( $this->__('Please select group(s)') );
		} else {
			try {
				foreach( $group_ids as $group_id ) {
					$model = Mage::getSingleton('slider/group')->load( $group_id )->setState( $this->getRequest()->getParam('state') )->setIsMassupdate( true )->save();
				}
				$this->_getSession()->addSuccess( $this->__('Total of %d group(s) were updated', count( $group_ids ) ) );
			} catch( Exception $e ) {
				$this->_getSession()->addError( $e->getMessage() );
			}
		}
		$this->_redirect('*/*/index');
	}
	public function exportCsvAction() {
		$content = $this->getLayout()->createBlock('slider/adminhtml_group_grid')->getCsv();
		$this->_prepareDownloadResponse('et_slider_group.csv', $content );
	}
	public function exportXmlAction() {
		$content = $this->getLayout()->createBlock('slider/adminhtml_group_grid')->getXml();
		$this->_prepareDownloadResponse('et_slider_groups.xml', $content );
	}
	
	protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
		$response = $this->getResponse();
		$response->setHeader('HTTP/1.1 200 OK', '');
		$response->setHeader('Pragma', 'public', true );
		$response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true );
		$response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName );
		$response->setHeader('Last-Modified', date('r') );
		$response->setHeader('Accept-Ranges', 'bytes');
		$response->setHeader('Content-Length', strlen( $content ) );
		$response->setHeader('Content-type', $contentType );
		$response->setBody( $content );
		$response->sendResponse();
		die();
	}
}