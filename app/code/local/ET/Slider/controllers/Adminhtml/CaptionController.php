<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Adminhtml_CaptionController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('et/slider/caption')
			->_addBreadcrumb(
				Mage::helper('slider')->__('Captions'),
				Mage::helper('slider')->__('Captions') )
			->_addBreadcrumb(
				Mage::helper('slider')->__('Manage Captions'),
				Mage::helper('slider')->__('Manage Captions') );
		return $this;
	}
	public function indexAction() {
		$this->_initAction()->renderLayout();
	}
	public function editAction() {
		$this->_title($this->__('Captions'))
			 ->_title($this->__('Manage Captions'));
		
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('slider/caption')->load($id);
		
		if($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if( !empty($data) ) {
				$model->setData($data);
			}
			
			Mage::register('caption_data', $model);
			
			$this->loadLayout();
			$this->_setActiveMenu('et/slider/caption');
			
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Captions'), Mage::helper('adminhtml')->__('Manage Captions'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Caption'), Mage::helper('adminhtml')->__('Caption'));
			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			
			$this->_addContent($this->getLayout()->createBlock('slider/adminhtml_caption_edit'))
				->_addLeft($this->getLayout()->createBlock('slider/adminhtml_caption_edit_tabs'));
			
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('slider')->__('Caption does not exist'));
			$this->_redirect('*/*/');
		}
	}
	public function newAction() {
		$this->_forward('edit');
	}
	public function saveAction() {
		if($data = $this->getRequest()->getPost()) {
			
			if ( isset($_FILES ['caption_image']['name']) && $_FILES ['caption_image']['name'] != '' ) {
				try {
					/* Starting upload */
					$uploader = new Varien_File_Uploader('caption_image');
					
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
					$path = Mage::getBaseDir('media') . '/et/slider/caption';
					$uploader->save( $path, $_FILES ['caption_image'] ['name'] );
				} catch( Exception $e ) {
				}
				
				// this way the name is saved in DB
				$data['caption_image'] = 'et/slider/caption/'.$_FILES ['caption_image']['name'];
			} else {
				if ( isset($data['caption_image']['delete']) && $data['caption_image']['delete'] ){
					if ( isset($data['caption_image']['value']) && !empty($data['caption_image']['value']) ){
						$path = Mage::getBaseDir('media') . '/' . $data['caption_image']['value'];
						if ( file_exists($path) ){
							if ( unlink($path) ){
								$data['caption_image'] = '';
							}
						}
						$data['caption_image'] = '';
					}
				}
			}
			
			if ( is_array($data['caption_image']) && isset($data['caption_image']['value']) ){
				$data['caption_image'] = $data['caption_image']['value'];
			}
			
			try {
				$model = Mage::getModel('slider/caption');
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
		Mage::getSingleton('adminhtml/session')->addError( Mage::helper('slider')->__('Unable to find caption to save') );
		$this->_redirect('*/*/');
	}
	public function deleteAction() {
		if($this->getRequest()->getParam('id') > 0) {
			try {
				Mage::getModel('slider/caption')->load( $this->getRequest()->getParam('id') )->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper('adminhtml')->__('Caption was deleted') );
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
		$caption_ids = $this->getRequest()->getParam('caption_param');
		if(! is_array( $caption_ids )) {
			Mage::getSingleton('adminhtml/session')->addError( Mage::helper('adminhtml')->__('Please select caption(s)') );
		} else {
			try {
				foreach( $caption_ids as $caption_id ) {
					Mage::getModel('slider/caption')->load( $caption_id )->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper('adminhtml')->__('Total of %d caption(s) were deleted', count( $caption_ids ) ) );
			} catch( Exception $e ) {
				Mage::getSingleton('adminhtml/session')->addError( $e->getMessage() );
			}
		}
		$this->_redirect('*/*/index');
	}
	public function massStateAction() {
		$caption_ids = $this->getRequest()->getParam('caption_param');
		if(! is_array( $caption_ids )) {
			Mage::getSingleton('adminhtml/session')->addError( $this->__('Please select caption(s)') );
		} else {
			try {
				foreach( $caption_ids as $caption_id ) {
					$model = Mage::getSingleton('slider/caption')->load( $caption_id )->setState( $this->getRequest()->getParam('state') )->setIsMassupdate( true )->save();
				}
				$this->_getSession()->addSuccess( $this->__('Total of %d caption(s) were updated', count( $caption_ids ) ) );
			} catch( Exception $e ) {
				$this->_getSession()->addError( $e->getMessage() );
			}
		}
		$this->_redirect('*/*/index');
	}
	public function exportCsvAction() {
		$content = $this->getLayout()->createBlock('slider/adminhtml_caption_grid')->getCsv();
		$this->_prepareDownloadResponse('et_slider_caption.csv', $content );
	}
	public function exportXmlAction() {
		$content = $this->getLayout()->createBlock('slider/adminhtml_caption_grid')->getXml();
		$this->_prepareDownloadResponse('et_slider_captions.xml', $content );
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