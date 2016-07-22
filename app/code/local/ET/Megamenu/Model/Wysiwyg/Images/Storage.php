<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_Wysiwyg_Images_Storage extends Mage_Cms_Model_Wysiwyg_Images_Storage
{
    /**
     * Return one-level child directories for specified path
     *
     * @param string $path Parent directory path
     * @return Varien_Data_Collection_Filesystem
     */
    public function getDirsCollection($path)
    {
        // Simple plugin fix provided by Tim Hengeveld - Lite Webdesigns - www.litewebdesigns.nl
        // Based on the idea of Clorne (http://www.magentocommerce.com/boards/member/9241/), as posted in this thread: http://www.magentocommerce.com/boards/viewthread/220720/P15/

        // These lines are removed to fix the displaying of folders in Wysiwyg editor when inserting an image.
        // Simply remove this extension once Magento has fixed this bug: http://www.magentocommerce.com/bug-tracking/issue?issue=11242
        /*$subDirectories = Mage::getModel('core/file_storage_directory_database')->getSubdirectories($path);

        foreach ($subDirectories as $directory) {
            $fullPath = rtrim($path, DS) . DS . $directory['name'];
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
        }*/

        $conditions = array('reg_exp' => array(), 'plain' => array());

        foreach ($this->getConfig()->dirs->exclude->children() as $dir) {
            $conditions[$dir->getAttribute('regexp') ? 'reg_exp' : 'plain'][(string) $dir] = true;
        }
        // \"include\" section takes precedence and can revoke directory exclusion
        foreach ($this->getConfig()->dirs->include->children() as $dir) {
            unset($conditions['regexp'][(string) $dir], $conditions['plain'][(string) $dir]);
        }

        $regExp = $conditions['reg_exp'] ? ('~' . implode('|', array_keys($conditions['reg_exp'])) . '~i') : null;
        $collection = $this->getCollection($path)
            ->setCollectDirs(true)
            ->setCollectFiles(false)
            ->setCollectRecursively(false);
        $storageRootLength = strlen($this->getHelper()->getStorageRoot());

        foreach ($collection as $key => $value) {
            $rootChildParts = explode(DIRECTORY_SEPARATOR, substr($value->getFilename(), $storageRootLength));

            if (array_key_exists($rootChildParts[0], $conditions['plain'])
                || ($regExp && preg_match($regExp, $value->getFilename()))) {
                $collection->removeItemByKey($key);
            }
        }

        return $collection;
    }
}