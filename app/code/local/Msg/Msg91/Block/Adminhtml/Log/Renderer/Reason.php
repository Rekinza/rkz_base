
<?php
class Msg_Msg91_Block_Adminhtml_Log_Renderer_Reason extends  Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }

    public function _getValue(Varien_Object $row)
    {
       $val = $row->getData($this->getColumn()->getIndex());
       $id = $row->getData('id');
      if($val==2){
         return $collection = Mage::getModel('msg91/log')->load($id)->getApiStatus();
      }
	else{
	echo "-";
	}

    }
}
?>
