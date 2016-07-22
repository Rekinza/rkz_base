<?php

class Msg_Msg91_Model_customnotification extends Mage_Core_Model_Abstract
{
      public function __construct()
        {
            $this->_init('msg91/customnotification');
        }
           public function checkAlreadyExists($data)
        {
            
         $collections=$this->getCollection();
            if($collections->getSize())
            {
                $valid=0;
                foreach($collections as $collection)
                {
                    //if(($data['title']==$collection->getZoneFromTransition()) && ($data['']==$collection->getZoneToTransition()))
                    //{
                    //    return 1;
                    //}
                    //elseif(($data['zone_from_transition']==$collection->getZoneToTransition()) && ($data['zone_to_transition']==$collection->getZoneFromTransition()))
                    //{
                    //    return 1;
                    //}
                    //
                    //else
                    //{
                    //    
                    //    $valid=$valid+1;
                    //}
                }
                if($collections->getSize()==$valid)
                {
                    return 0;
                }
            }
            return $collections->getSize();
        }
         
}
?>
