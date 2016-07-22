<?php

$installer = $this;

$this->startSetup();

$this->run("
 CREATE TABLE `{$this->getTable('msg91/notificationlog')}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `sent_date` timestamp default '0000-00-00 00:00:00',
   `sender_id` varchar(20) default NULL,
 `to` varchar(20) default NULL,
 `recipient` varchar(100) default NULL,
  `chars` int(6) NOT NULL default '0',
  `length` int(6) NOT NULL default '0',
  `status` int(6) NOT NULL default '0',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
");

$this->endSetup();
?>