<?php
class Thredshare_Catalog_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    public function setCollection($collection)
    {
        parent::setCollection($collection);
 
        if ($this->getCurrentOrder()) {
            if($this->getCurrentOrder() == 'popularity') {
                $this->getCollection()->getSelect()
                     ->joinLeft(
                            array('sfoi' => $collection->getResource()->getTable('reports/event')),
                             'e.entity_id = sfoi.object_id',
                             array('views' => 'count(sfoi.event_id)')
                         )
					 ->where('sfoi.event_type_id=1')
                     ->group('e.entity_id')
                     ->order('views desc');
					 
            }
			else if ($this->getCurrentOrder() == 'new'){
			$this->getCollection()->setOrder('entity_id','desc');
			
			}
        }
 
        return $this;
    }
}
?>