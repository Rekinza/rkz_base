<?php

class Returns_Returns_ReturnsController extends Mage_Core_Controller_Front_Action
{
	public function returnsAction()
	{
		//Get current layout state
		$this->loadLayout();
		
		$block = $this->getLayout()->createBlock(
			'Mage_Core_Block_Template',
			'returns',
			array('template' => 'thredshare/returns/returns.phtml')
		);
		
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
		$this->getLayout()->getBlock('content')->append($block);
		$this->_initLayoutMessages('core/session');
		$this->renderLayout();
		
	}
	
}

?>