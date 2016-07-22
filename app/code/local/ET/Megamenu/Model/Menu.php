<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_Menu extends Mage_Core_Model_Abstract {
    protected $_location;
    protected $_location_id;
    protected $_cache = array();
    
    protected function _construct() {
        $this->_init('megamenu/menu');
    }
    
    public function log($message){
        Mage::log($message, Zend_Log::DEBUG, 'menu.log');
    }
    
    /**
     * If object is new adds creation date
     *
     * @return ET_Megamenu_Model_Menu
     */
    protected function _beforeSave() {
        $this->log(__METHOD__);
        parent::_beforeSave ();
        
        if ($this->isObjectNew()) {
            $this->setData('created_at', Varien_Date::now());
        }
        
        $parent_id = $this->getParentId();
        if (! $parent_id) {
            $parent_id = $this->getRootId();
        }
        
        $ordering = $this->getData('ordering');
        if (is_numeric($ordering)) {
            $this->setLocation($ordering, 'before');
        } else {
            if (($ordering != 'before') && ($ordering != 'after') && ($ordering != 'first-child') && ($ordering != 'last-child')) {
                $ordering = 'last-child';
            }
            $this->setLocation($parent_id, $ordering);
        }
        
        /*
         * If the primary key is empty, then we assume we are inserting a new node into the tree. From this point we would need to determine where in the tree to insert it.
         */
        $table = Mage::getSingleton('core/resource')->getTableName('megamenu/menu');
        if ($this->isObjectNew()) {
            /*
             * We are inserting a node somewhere in the tree with a known reference node. We have to make room for the new node and set the left and right values before we insert the row.
             */
            if ($this->_location_id >= 0) {
                // except root menu
                if ($this->getParentId ()) {
                    // We are inserting a node relative to the last root node.
                    if ($this->_location_id == 0) {
                        // Get the last root node as the reference node.
                        $sql = "SELECT id, parent_id, depth, lft, rgt FROM {$table} WHERE parent_id = 0 AND position_id = {$this->getPositionId()} ORDER BY lft DESC LIMIT 0, 1";
                        $reference = ( object ) Mage::getSingleton('core/resource')->getConnection ( 'core_read' )->fetchRow ( $sql );
                    }                     // We have a real node set as a location reference.
                    else {
                        // Get the reference node by primary key.
                        if (! $reference = $this->_getNode ( $this->_location_id )) {
                            // Error message set in getNode method.
                            return false;
                        }
                    }
                    
                    // Get the reposition data for shifting the tree and re-inserting the node.
                    if (! ($repositionData = $this->_getTreeRepositionData ( $reference, 2, $this->_location ))) {
                        // Error message set in getNode method.
                        return false;
                    }
                    
                    // Create space in the tree at the new location for the new node in left ids.
                    $sql = "UPDATE {$table} SET lft = lft + 2 WHERE {$repositionData->left_where} AND position_id = " . $this->getPositionId ();
                    // Mage::getSingleton('adminhtml/session')->addError("Error 1: $sql");
                    Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
                    
                    // Create space in the tree at the new location for the new node in right ids.
                    $sql = "UPDATE {$table} SET rgt = rgt + 2 WHERE {$repositionData->right_where} AND position_id = " . $this->getPositionId ();
                    // Mage::getSingleton('adminhtml/session')->addError("Error 2: $sql");
                    Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
                    
                    // Set the object values.
                    $this->setParentId ( $repositionData->new_parent_id );
                    $this->setDepth ( $repositionData->new_depth );
                    $this->setLft ( $repositionData->new_lft );
                    $this->setRgt ( $repositionData->new_rgt );
                }
            } else {
                // Negative parent ids are invalid
                return false;
            }
        }         /*
         * If we have a given primary key then we assume we are simply updating this node in the tree. We should assess whether or not we are moving the node or just updating its data fields.
         */
        else {
            // If the location has been set, move the node to its new location.
            if ($this->_location_id > 0) {
                if (! $this->moveByReference ( $this->_location_id, $this->_location, $this->getId () )) {
                    // Error message set in move method.
                    return false;
                }
            }
        }
        
        return $this;
    }
    protected function _beforeDelete() {
        $this->log(__METHOD__);
        
        parent::_beforeDelete ();
        
        $deleteChildren = false;
        $pk = $this->getId ();
        if (! $pk || ! $this->getPositionId ()) {
            return false;
        }
        // Get the node by id.
        $node = $this->_getNode ( $pk );
        if ($node === false) {
            // Error message set in getNode method.
            return false;
        }
        
        $table = Mage::getSingleton('core/resource')->getTableName('megamenu/menu');
        
        // Should we delete all children along with the node?
        if ($deleteChildren) {
            // Delete the node and all of its children.
            $sql = 'DELETE FROM ' . $table . ' WHERE (lft BETWEEN ' . (int)$node->lft . ' AND ' . (int)$node->rgt . ') AND position_id = ' . $this->getPositionId ();
            // Mage::getSingleton('adminhtml/session')->addError("Error 3: $sql");
            Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
            
            // Compress the left values.
            $sql = 'UPDATE ' . $table . ' SET lft = lft - ' . (int)$node->width . ' WHERE lft > ' . (int)$node->rgt . ' AND position_id = ' . $this->getPositionId ();
            // Mage::getSingleton('adminhtml/session')->addError("Error 4: $sql");
            Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
            
            // Compress the right values.
            $sql = 'UPDATE ' . $table . ' SET rgt = rgt - ' . (int)$node->width . ' WHERE rgt > ' . (int)$node->rgt . ' AND position_id = ' . $this->getPositionId ();
            // Mage::getSingleton('adminhtml/session')->addError("Error 5: $sql");
            Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
        }         // Leave the children and move them up a depth.
        else {
            // Delete the node.
            $sql = 'DELETE FROM ' . $table . ' WHERE lft = ' . (int)$node->lft . ' AND position_id = ' . $this->getPositionId ();
            // Mage::getSingleton('adminhtml/session')->addError("Error 6: $sql");
            Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
            
            // Shift all node's children up a depth.
            $sql = 'UPDATE ' . $table . ' SET lft = lft - 1, rgt = rgt - 1, depth = depth - 1' . ' WHERE lft BETWEEN ' . (int)$node->lft . ' AND ' . (int)$node->rgt . ' AND position_id = ' . $this->getPositionId ();
            // Mage::getSingleton('adminhtml/session')->addError("Error 7: $sql");
            Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
            
            // Adjust all the parent values for direct children of the deleted node.
            $sql = 'UPDATE ' . $table . ' SET parent_id = ' . (int)$node->parent_id . ' WHERE parent_id = ' . (int)$node->id . ' AND position_id = ' . $this->getPositionId ();
            // Mage::getSingleton('adminhtml/session')->addError("Error 8: $sql");
            Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
            
            // Shift all of the left values that are right of the node.
            $sql = 'UPDATE ' . $table . ' SET lft = lft - 2 WHERE lft > ' . (int)$node->rgt . ' AND position_id = ' . $this->getPositionId ();
            // Mage::getSingleton('adminhtml/session')->addError("Error 9: $sql");
            Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
            
            // Shift all of the right values that are right of the node.
            $sql = 'UPDATE ' . $table . ' SET rgt = rgt - 2 WHERE rgt > ' . (int)$node->rgt . ' AND position_id = ' . $this->getPositionId ();
            // Mage::getSingleton('adminhtml/session')->addError("Error 10: $sql");
            Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
        }
        
        return true;
    }
    public function getPath($pk = null, $diagnostic = false) {
        $this->log(__METHOD__);
        $pk = (is_null ( $pk )) ? $this->getId () : $pk;
        
        $collection = new Varien_Data_Collection ();
        $table = Mage::getSingleton('core/resource')->getTableName('megamenu/menu');
        
        $select = ($diagnostic) ? 'p.id, p.parent_id, p.depth, p.lft, p.rgt' : 'p.*';
        $sql = 'SELECT ' . $select;
        $sql .= " FROM {$table} AS n, {$table} AS p ";
        $sql .= " WHERE (n.lft BETWEEN p.lft AND p.rgt) AND n.id = {$pk} AND p.position_id = " . $this->getPositionId ();
        $sql .= " ORDER BY p.lft ";
        try {
            $_items = Mage::getSingleton('core/resource')->getConnection ( 'core_read' )->fetchAll ( $sql );
            foreach ( $_items as $_item ) {
                $collection->addItem ( new Varien_Object ( $_item ) );
            }
        } catch ( Exception $e ) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
        }
        return $collection;
    }
    public function mayBeParents($pk = null, $diagnostic = false) {
        $this->log(__METHOD__);
        $pk = (is_null ( $pk )) ? $this->getId () : $pk;
        
        $collection = new Varien_Data_Collection ();
        $table = Mage::getSingleton('core/resource')->getTableName('megamenu/menu');
        
        // Get the node and children as a tree.
        $select = ($diagnostic) ? 'n.id, n.parent_id, n.depth, n.lft, n.rgt' : 'n.*';
        $sql = 'SELECT ' . $select;
        $sql .= " FROM {$table} AS n, {$table} AS s ";
        $sql .= " WHERE ((n.lft < s.lft) OR (n.rgt > s.rgt)) AND s.id = {$pk} AND n.position_id = " . $this->getPositionId ();
        $sql .= " ORDER BY n.lft ";
        
        try {
            $_items = Mage::getSingleton('core/resource')->getConnection ( 'core_read' )->fetchAll ( $sql );
            foreach ( $_items as $_item ) {
                $collection->addItem ( new Varien_Object ( $_item ) );
            }
        } catch ( Exception $e ) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
        }
        return $collection;
    }
    public function getTree($pk = null, $diagnostic = false) {
        $this->log(__METHOD__);
        $pk = (is_null ( $pk )) ? $this->getId () : $pk;
        
        $collection = new Varien_Data_Collection ();
        $table = Mage::getSingleton('core/resource')->getTableName('megamenu/menu');
        
        // Get the node and children as a tree.
        $select = ($diagnostic) ? 'n.id, n.parent_id, n.depth, n.lft, n.rgt' : 'n.*';
        $sql = 'SELECT ' . $select;
        $sql .= " FROM {$table} AS n, {$table} AS p ";
        $sql .= " WHERE (n.lft BETWEEN p.lft AND p.rgt) AND p.id = {$pk} AND n.position_id = " . $this->getPositionId ();
        $sql .= " ORDER BY n.lft ";
        
        try {
            $_items = Mage::getSingleton('core/resource')->getConnection ( 'core_read' )->fetchAll ( $sql );
            foreach ( $_items as $_item ) {
                $collection->addItem ( new Varien_Object ( $_item ) );
            }
        } catch ( Exception $e ) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
        }
        return $collection;
    }
    public function getOrdering($pk = null, $diagnostic = false) {
        $this->log(__METHOD__);
        $pk = (is_null ( $pk )) ? $this->getId () : $pk;
        
        $collection = new Varien_Data_Collection ();
        $table = Mage::getSingleton('core/resource')->getTableName('megamenu/menu');
        
        // Get the node and children as a tree.
        $select = ($diagnostic) ? 'n.id, n.parent_id, n.depth, n.lft, n.rgt' : 'n.*';
        $sql = 'SELECT ' . $select;
        $sql .= " FROM {$table} AS n";
        $sql .= " WHERE n.parent_id = {$pk} AND n.position_id = " . $this->getPositionId ();
        $sql .= " ORDER BY n.lft ";
        
        try {
            $_items = Mage::getSingleton('core/resource')->getConnection ( 'core_read' )->fetchAll ( $sql );
            foreach ( $_items as $_item ) {
                $collection->addItem ( new Varien_Object ( $_item ) );
            }
        } catch ( Exception $e ) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
        }
        return $collection;
    }
    public function isLeaf($pk = null) {
        $this->log(__METHOD__);
        $pk = (is_null ( $pk )) ? $this->getId () : $pk;
        $node = $this->_getNode ( $pk );
        
        // Get the node by primary key.
        if ($node === false) {
            // Error message set in getNode method.
            return null;
        }
        
        // The node is a leaf node.
        return (($node->getRgt () - $node->getLft ()) == 1);
    }
    protected function _getNode($id, $key = null) {
        $this->log(__METHOD__);
        // Determine which key to get the node base on.
        switch ($key) {
            case 'parent' :
                $k = 'parent_id';
                break;
            
            case 'left' :
                $k = 'lft';
                break;
            
            case 'right' :
                $k = 'rgt';
                break;
            
            default :
                $k = 'id';
                break;
        }
        $table = Mage::getSingleton('core/resource')->getTableName('megamenu/menu');
        $sql = 'SELECT id, parent_id, depth, lft, rgt FROM ' . $table;
        $sql .= ' WHERE ' . $k . ' = ' . (int)$id . ' AND position_id = ' . $this->getPositionId ();
        $sql .= ' LIMIT 0, 1';
        // Mage::getSingleton('adminhtml/session')->addError("Error 11: $sql");
        // Magento_Db_Adapter_Pdo_Mysql
        $row = ( object ) Mage::getSingleton('core/resource')->getConnection ( 'core_read' )->fetchRow ( $sql );
        if (is_object ( $row )) {
            $row->num_children = (int)($row->rgt - $row->lft - 1) / 2;
            $row->width = (int)$row->rgt - $row->lft + 1;
            
            return $row;
        }
        return false;
    }
    public function getRootId() {
        $this->log(__METHOD__);
        $table = Mage::getSingleton('core/resource')->getTableName('megamenu/menu');
        
        $sql = 'SELECT id FROM ' . $table . ' WHERE parent_id = 0 AND position_id = ' . $this->getPositionId ();
        $result = Mage::getSingleton('core/resource')->getConnection ( 'core_read' )->fetchCol ( $sql );
        // var_dump($result); die;
        if (count ( $result ) == 1) {
            return $result [0];
        }
        
        $sql = 'SELECT id FROM ' . $table . ' WHERE lft = 0 AND position_id = ' . $this->getPositionId ();
        $result = Mage::getSingleton('core/resource')->getConnection ( 'core_read' )->fetchCol ( $sql );
        // var_dump($result); die;
        if (count ( $result ) == 1) {
            return $result [0];
        }
        
        return false;
    }
    public function setLocation($referenceId, $position = 'after') {
        $this->log(__METHOD__);
        // Make sure the location is valid.
        if (($position != 'before') && ($position != 'after') && ($position != 'first-child') && ($position != 'last-child')) {
            throw new InvalidArgumentException ( sprintf ( '%s::setLocation(%d, *%s*)', get_class ( $this ), $referenceId, $position ) );
        }
        
        // Set the location properties.
        $this->_location = $position;
        $this->_location_id = $referenceId;
    }
    public function move($delta, $where = '') {
        $this->log(__METHOD__);
        $pk = $this->getId ();
        
        $sql = 'SELECT id FROM ' . $table . ' WHERE parent_id = ' . $this->getParentId () . ' AND position_id = ' . $this->getPositionId ();
        // Mage::getSingleton('adminhtml/session')->addError("Error 12: $sql");
        
        if ($where) {
            $sql .= ' AND ' . $where;
        }
        
        if ($delta > 0) {
            $sql .= ' AND rgt > ' . $this->rgt . ' AND position_id = ' . $this->getPositionId () . ' ORDER BY rgt ASC';
            $position = 'after';
        } else {
            $sql .= ' AND lft < ' . $this->lft . ' AND position_id = ' . $this->getPositionId () . ' ORDER BY lft DESC';
            $position = 'before';
        }
        // Mage::getSingleton('adminhtml/session')->addError("Error 13: $sql");
        $referenceId = Mage::getSingleton('core/resource')->getConnection ( 'core_read' )->fetchOne ( $sql );
        
        if ($referenceId) {
            return $this->moveByReference ( $referenceId, $position, $pk );
        } else {
            return false;
        }
    }
    
    public function moveByReference($referenceId, $position = 'after', $pk = null) {
        $this->log(__METHOD__);
        $pk = (is_null ( $pk )) ? $this->getId () : $pk;
        $table = Mage::getSingleton('core/resource')->getTableName('megamenu/menu');
        
        // Get the node by id.
        if (! $node = $this->_getNode ( $pk )) {
            return false;
        }
        
        // Get the ids of child nodes.
        $sql = 'SELECT id FROM ' . $table . ' WHERE (lft BETWEEN ' . (int)$node->lft . ' AND ' . (int)$node->rgt . ') AND position_id = ' . $this->getPositionId ();
        // Mage::getSingleton('adminhtml/session')->addError("Error 15: $sql");
        $children = Mage::getSingleton('core/resource')->getConnection ( 'core_read' )->fetchCol ( $sql );
        
        // Cannot move the node to be a child of itself.
        if (in_array ( $referenceId, $children )) {
            // Mage::getSingleton ( 'adminhtml/session' )->addError ( sprintf ( '%s::moveByReference(%d, %s, %d) parenting to child.', get_class ( $this ), $referenceId, $position, $pk ) );
            return false;
        }
        
        /*
         * Move the sub-tree out of the nested sets by negating its left and right values.
         */
        $sql = 'UPDATE ' . $table . ' SET lft = lft * (-1), rgt = rgt * (-1) WHERE lft BETWEEN ' . (int)$node->lft . ' AND ' . (int)$node->rgt . ' AND position_id = ' . $this->getPositionId ();
        // Mage::getSingleton('adminhtml/session')->addError("Error 16: $sql");
        Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
        
        /*
         * Close the hole in the tree that was opened by removing the sub-tree from the nested sets.
         */
        // Compress the left values.
        $sql = 'UPDATE ' . $table . ' SET lft = lft - ' . (int)$node->width . ' WHERE lft > ' . (int)$node->rgt . ' AND position_id = ' . $this->getPositionId ();
        // Mage::getSingleton('adminhtml/session')->addError("Error 17: $sql");
        Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
        
        // Compress the right values.
        $sql = 'UPDATE ' . $table . ' SET rgt = rgt - ' . (int)$node->width . ' WHERE rgt > ' . (int)$node->rgt . ' AND position_id = ' . $this->getPositionId ();
        // Mage::getSingleton('adminhtml/session')->addError("Error 18: $sql");
        Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
        
        // We are moving the tree relative to a reference node.
        if ($referenceId) {
            // Get the reference node by primary key.
            if (! $reference = $this->_getNode ( $referenceId )) {
                // Error message set in getNode method.
                return false;
            }
            
            // Get the reposition data for shifting the tree and re-inserting the node.
            if (! $repositionData = $this->_getTreeRepositionData ( $reference, $node->width, $position )) {
                return false;
            }
        }         // We are moving the tree to be the last child of the root node
        else {
            // Get the last root node as the reference node.
            $sql = 'SELECT id, parent_id, depth, lft, rgt FROM ' . $table . ' WHERE parent_id = 0 AND position_id = ' . $this->getPositionId () . ' ORDER BY lft DESC';
            $reference = ( object ) Mage::getSingleton('core/resource')->getConnection ( 'core_read' )->fetchRow ( $sql );
            
            // Get the reposition data for re-inserting the node after the found root.
            if (! $repositionData = $this->_getTreeRepositionData ( $reference, $node->width, 'last-child' )) {
                // Error message set in getNode method.
                return false;
            }
        }
        
        /*
         * Create space in the nested sets at the new location for the moved sub-tree.
         */
        
        // Shift left values.
        $sql = 'UPDATE ' . $table . ' SET lft = lft + ' . (int)$node->width . ' WHERE ' . $repositionData->left_where . ' AND position_id = ' . $this->getPositionId ();
        // Mage::getSingleton('adminhtml/session')->addError("Error 19: $sql");
        Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
        
        // Shift right values.
        $sql = 'UPDATE ' . $table . ' SET rgt = rgt + ' . (int)$node->width . ' WHERE ' . $repositionData->right_where . ' AND position_id = ' . $this->getPositionId ();
        // Mage::getSingleton('adminhtml/session')->addError("Error 20: $sql");
        Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
        
        /*
         * Calculate the offset between where the node used to be in the tree and where it needs to be in the tree for left ids (also works for right ids).
         */
        $offset = $repositionData->new_lft - $node->lft;
        $depthOffset = $repositionData->new_depth - $node->depth;
        
        // Move the nodes back into position in the tree using the calculated offsets.
        $sql = 'UPDATE ' . $table . ' SET rgt = ' . (int)$offset . ' - rgt, lft = ' . (int)$offset . ' - lft, depth = depth + ' . (int)$depthOffset . ' WHERE lft < 0 AND position_id = ' . $this->getPositionId ();
        Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
        
        // Set the correct parent id for the moved node if required.
        if ($node->parent_id != $repositionData->new_parent_id) {
            
            $sql = 'UPDATE ' . $table . ' SET parent_id = ' . (int)$repositionData->new_parent_id . ' WHERE id = ' . (int)$node->id . ' AND position_id = ' . $this->getPositionId ();
            // Mage::getSingleton('adminhtml/session')->addError("Error 21: $sql");
            Mage::getSingleton('core/resource')->getConnection ( 'core_write' )->exec ( $sql );
        }
        
        // Set the object values.
        $this->parent_id = $repositionData->new_parent_id;
        $this->depth = $repositionData->new_depth;
        $this->lft = $repositionData->new_lft;
        $this->rgt = $repositionData->new_rgt;
        
        return true;
    }
    protected function _getTreeRepositionData($referenceNode, $nodeWidth, $position = 'before') {
        $this->log(__METHOD__);
        // Make sure the reference an object with a left and right id.
        if (! is_object ( $referenceNode ) || ! (isset ( $referenceNode->lft ) || ! isset ( $referenceNode->rgt ))) {
            return false;
        }
        
        // A valid node cannot have a width less than 2.
        if ($nodeWidth < 2) {
            return false;
        }
        
        $data = new stdClass ();
        
        // Run the calculations and build the data object by reference position.
        switch ($position) {
            case 'first-child' :
                $data->left_where = 'lft > ' . $referenceNode->lft;
                $data->right_where = 'rgt >= ' . $referenceNode->lft;
                
                $data->new_lft = $referenceNode->lft + 1;
                $data->new_rgt = $referenceNode->lft + $nodeWidth;
                $data->new_parent_id = $referenceNode->id;
                $data->new_depth = $referenceNode->depth + 1;
                break;
            
            case 'last-child' :
                $data->left_where = 'lft > ' . ($referenceNode->rgt);
                $data->right_where = 'rgt >= ' . ($referenceNode->rgt);
                
                $data->new_lft = $referenceNode->rgt;
                $data->new_rgt = $referenceNode->rgt + $nodeWidth - 1;
                $data->new_parent_id = $referenceNode->id;
                $data->new_depth = $referenceNode->depth + 1;
                break;
            
            case 'before' :
                $data->left_where = 'lft >= ' . $referenceNode->lft;
                $data->right_where = 'rgt >= ' . $referenceNode->lft;
                
                $data->new_lft = $referenceNode->lft;
                $data->new_rgt = $referenceNode->lft + $nodeWidth - 1;
                $data->new_parent_id = $referenceNode->parent_id;
                $data->new_depth = $referenceNode->depth;
                break;
            
            default :
            case 'after' :
                $data->left_where = 'lft > ' . $referenceNode->rgt;
                $data->right_where = 'rgt > ' . $referenceNode->rgt;
                
                $data->new_lft = $referenceNode->rgt + 1;
                $data->new_rgt = $referenceNode->rgt + $nodeWidth;
                $data->new_parent_id = $referenceNode->parent_id;
                $data->new_depth = $referenceNode->depth;
                break;
        }
        
        return $data;
    }
    
    
    public function rebuild($parentId = null, $leftId = 0, $level = 0, $path = '') {
        $this->log(__METHOD__);
        $table = Mage::getSingleton('core/resource')->getTableName('megamenu/menu');
        // If no parent is provided, try to find it.
        if ($parentId === null) {
            // Get the root item.
            $parentId = $this->getRootId();
                
            if ($parentId === false) {
                return false;
            }
        }
    
        // Build the structure of the recursive query.
        if (! isset ( $this->_cache ['rebuild.sql'] )) {
            $query = "SELECT id FROM {$table} WHERE parent_id = %d ";
                
            // If the table has an ordering field, use that for ordering.
            if ( isset($this->ordering) ){
                $query .= ' ORDER BY parent_id, ordering, lft';
            } else {
                $query .= ' ORDER BY parent_id, lft';
            }
            $this->_cache ['rebuild.sql'] = $query;
        }
    
        // Make a shortcut to database object.
    
        // Assemble the query to find all children of this node.
        $children = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll( sprintf($this->_cache['rebuild.sql'], (int)$parentId) );
    
        // The right value of this node is the left value + 1
        $rightId = $leftId + 1;
    
        // Execute this function recursively over all children
        foreach ( $children as $node ) {
            //var_dump($node);die;
            /*
             * $rightId is the current right value, which is incremented on recursion return. Increment the level for the children. Add this item's alias to the path (but avoid a leading /)
            */
            $rightId = $this->rebuild ( $node['id'], $rightId, $level + 1, $path );
                
            // If there is an update failure, return false to break out of the recursion.
            if ($rightId === false) {
                return false;
            }
        }
    
        // We've got the left value, and now that we've processed
        // the children of this node we also know the right value.
        $sql = "UPDATE {$table}
            SET lft = " . (int)$leftId . ",
                rgt = " . (int)$rightId . ",
                depth = " . (int)$level . "
                    WHERE id = " . (int)$parentId
        ;
        Mage::getSingleton('core/resource')->getConnection('core_write')->exec($sql);
        // Return the right value of this node + 1.
        return $rightId + 1;
    }
}