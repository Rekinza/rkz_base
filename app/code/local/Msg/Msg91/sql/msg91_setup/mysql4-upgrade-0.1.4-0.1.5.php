<?php
$installer = $this;

$installer->startSetup();

$installer->run("
                ALTER TABLE `".$this->getTable('msg91/log')."` ADD `api_status` TEXT NOT NULL AFTER `status`");

$installer->endSetup();
?>