<?php
$installer = $this;

$installer->startSetup();

$installer->run("
                ALTER TABLE `".$this->getTable('msg91/customnotification')."` ADD `route` TEXT NOT NULL AFTER `is_active`");

$installer->endSetup();
?>