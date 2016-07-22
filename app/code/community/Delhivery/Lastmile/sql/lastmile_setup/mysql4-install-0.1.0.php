<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2014 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Mysql Install Script for module
 */
$installer = $this;

$installer->startSetup();
/**
* installer query to setup database table at the time of module installation
*/
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('delhivery_lm_awb')};
CREATE TABLE {$this->getTable('delhivery_lm_awb')} (
  `lastmile_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `awb` varchar(255) NOT NULL DEFAULT '',
  `shipment_id` int(11) NOT NULL,
  `shipment_to` varchar(255) NOT NULL DEFAULT '',
  `state` tinyint(1) NOT NULL DEFAULT '2' COMMENT '2= Unused, 1= Used',
  `orderid` varchar(20) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_time` DATETIME NULL,
  `update_time` DATETIME NULL,
  PRIMARY KEY (`lastmile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
-- DROP TABLE IF EXISTS {$this->getTable('delhivery_lm_pincode')};
CREATE TABLE {$this->getTable('delhivery_lm_pincode')} (
 `pincode_id` int(11) unsigned NOT NULL auto_increment,
 `district` varchar(255) NOT NULL default '',
 `pin` varchar(20) NOT NULL default '',
 `pre_paid` varchar(5) NOT NULL default '', 
 `cash` varchar(5) NOT NULL default '',
 `pickup` varchar(5) NOT NULL default '',
 `cod` varchar(5) NOT NULL default '',
 `state_code` varchar(5) NOT NULL default '',    
 `status` smallint(6) NOT NULL default '1',
 `created_time` DATETIME NULL,
 `update_time` DATETIME NULL,
 PRIMARY KEY (`pincode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();
