<?php
$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE  `".$this->getTable('msg91/log')."` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");    


$installer->run("
                ALTER TABLE `".$this->getTable('msg91/log')."` ADD `msg_content` TEXT NOT NULL AFTER `recipient`");
$installer->endSetup();
?>