<?php
$installer = $this;
$installer->startSetup();
$installer->run(
    "
DROP TABLE IF EXISTS {$this->getTable('sociallogin')};
CREATE TABLE IF NOT EXISTS {$this->getTable('sociallogin')} (
   `sociallogin_id` varchar(200) default NULL,
  `entity_id` int(11) default NULL,
  `avatar` varchar(1000) default NULL,
  `verified` enum('0','1') default NULL,
  `vkey` varchar(40) default NULL,
  `provider` varchar(20) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    "
);
$installer->endSetup();