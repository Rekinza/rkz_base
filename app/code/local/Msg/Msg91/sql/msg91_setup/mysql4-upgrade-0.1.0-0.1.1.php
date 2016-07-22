<?php

$installer = $this;

$this->startSetup();

$this->run("
 CREATE TABLE `{$installer->getTable('msg91/customnotification')}` (
  `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `content` varchar(510) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `customer_group` blob DEFAULT '',
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
");

$this->endSetup();
