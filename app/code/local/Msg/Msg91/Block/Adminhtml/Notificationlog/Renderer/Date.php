
<?php
class Msg_Msg91_Block_Adminhtml_Notificationlog_Renderer_Date extends  Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }

    public function _getValue(Varien_Object $row)
    {
        $val = $row->getData($this->getColumn()->getIndex());
        $date= strtotime($val);
        $newformat = date('d/m/y H:i:s',$date);
   
        return $newformat;

    }
}
?>