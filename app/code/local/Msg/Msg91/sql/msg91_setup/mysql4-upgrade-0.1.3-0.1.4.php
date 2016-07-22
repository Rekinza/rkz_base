<?php
$installer = $this;

$installer->startSetup();

$installer->run("
                ALTER TABLE `".$this->getTable('msg91/customnotification')."` ADD `sender_id` TEXT NOT NULL AFTER `route`");

$installer->endSetup();
?>