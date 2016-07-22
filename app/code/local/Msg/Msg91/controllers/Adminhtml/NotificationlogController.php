<?php
 
class Msg_Msg91_Adminhtml_NotificationlogController extends Mage_Adminhtml_Controller_Action
{
 
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('msg91/items');
        $this->renderLayout();
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }
 
    public function editAction()
    {
	$id     = $this->getRequest()->getParam('id');
	$model  = Mage::getModel('msg91/Notificationlog')->load($id);

	if ($model->getId() || $id == 0)
	{
	    $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
	    if (!empty($data)) {
		    $model->setData($data);
	    }

	    Mage::register('notificationlog_data', $model);

	    $this->loadLayout();
	    $this->_setActiveMenu('msg91/items');

	    $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
	    $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

	    $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
	    
	    $this->_addContent($this->getLayout()->createBlock('msg91/adminhtml_notificationlog_edit'))
		    ->_addLeft($this->getLayout()->createBlock('msg91/adminhtml_notificationlog_edit_tabs'));

		$this->renderLayout();
	} else {
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('msg91')->__('Item does not exist'));
		$this->_redirect('*/*/');
	}
    }
 
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
        {
            $model = Mage::getModel('msg91/notificationlog');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);
 
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }
                $model->save();
 
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('msg91')->__('Error saving notification log'));
                }
 
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('msg91')->__('Notification log was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
 
                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
 
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($model && $model->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }
 
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('msg91')->__('No data found to save'));
        $this->_redirect('*/*/');
    }
 
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('msg91/notificationlog');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('msg91')->__('The notification log has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the notification log to delete.'));
        $this->_redirect('*/*/');
    }
    
    public function massDeleteAction() {
        $nlogIds = $this->getRequest()->getParam('msg91');
        if(!is_array($nlogIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($nlogIds as $nlogId) {
                    $nlog = Mage::getModel('msg91/notificationlog')->load($nlogId);
                    $nlog->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($nlogIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $nlogIds = $this->getRequest()->getParam('msg91');
        if(!is_array($nlogIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($nlogIds as $nlogId) {
                    $nlog = Mage::getSingleton('msg91/notificationlog')
                        ->load($nlogId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($nlogIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
        
    public function _isAllowed()
    {
        return true;
    }
}