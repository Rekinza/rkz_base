<?php
$installer = $this;

$installer->startSetup();

$installer->run("
                ALTER TABLE `".$this->getTable('msg91/notificationlog')."` ADD `api_status` TEXT NOT NULL AFTER `scheduled_at`");

$installer->endSetup();
?>