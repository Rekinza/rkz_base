<?php
 
$installer = $this;
 
$installer->startSetup();
 
$installer->run("
 
-- DROP TABLE IF EXISTS {$this->getTable('msg91/log')};
CREATE TABLE {$this->getTable('msg91/log')}(
  `id` int(11) unsigned NOT NULL auto_increment,
  `sent_date` timestamp default '0000-00-00 00:00:00',
  `order_no` int(11) NOT NULL default '0',
  `title` varchar(255) default NULL,
  `sender_id` varchar(20) default NULL,
  `to` varchar(20) default NULL,
  `recipient` varchar(100) default NULL,
  `country` text default NULL,
  `chars` int(6) NOT NULL default '0',
  `length` int(6) NOT NULL default '0',
  `status` int(6) NOT NULL default '0',
  PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

");
 
$installer->endSetup();
?>