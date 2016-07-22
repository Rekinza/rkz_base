<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('megamenu/menu')} (
		`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`title` VARCHAR(255) NOT NULL DEFAULT '',
		`subtitle` VARCHAR(255) NULL DEFAULT '',
		`description` TEXT NULL DEFAULT '',
		`target` VARCHAR(255) NULL DEFAULT '',
		`html_id` VARCHAR(255) NULL DEFAULT '',
		`html_class` VARCHAR(255) NULL DEFAULT '',
		`class_icon` VARCHAR(255) NULL DEFAULT '',
		`align` VARCHAR(255) NULL DEFAULT '',
		`menu_size` SMALLINT(6) NULL DEFAULT '12',
		`hide_title` SMALLINT(6) NULL DEFAULT '0',
		`state` SMALLINT(6) NOT NULL DEFAULT '0',
		`is_root` SMALLINT(11) NOT NULL DEFAULT '0',
		`position_id` INT(11) NOT NULL DEFAULT '0',
		
		`parent_id` INT(11) NOT NULL DEFAULT '0',
		`depth` INT(11) NOT NULL DEFAULT '0',
		`lft` INT(11) NOT NULL DEFAULT '0',
		`rgt` INT(11) NOT NULL DEFAULT '0',
		
		`menu_type` INT(11) NOT NULL DEFAULT '1',
		`external_link` VARCHAR(255) NULL DEFAULT '',
		`product_link` VARCHAR(255) NULL DEFAULT '',
		`category_link` VARCHAR(255) NULL DEFAULT '',
		`cmspage_link` VARCHAR(255) NULL DEFAULT '',
		`magento_route` VARCHAR(255) NULL DEFAULT '',
		
		`dropdown_hover` INT(11) NOT NULL DEFAULT '1',
		`dropdown_content` INT(11) NOT NULL DEFAULT '0',
		`dropdown_layout` VARCHAR(255) NULL DEFAULT 'default',
		`dropdown_size` VARCHAR(255) NOT NULL DEFAULT '',

		`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('megamenu/position')} (
		`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`title` VARCHAR(255) NOT NULL DEFAULT '',
		`description` TEXT NULL DEFAULT '',
		`align` VARCHAR(255) NULL DEFAULT '',
		`state` SMALLINT(6) NOT NULL DEFAULT '0',

		`use_container` INT(11) NULL DEFAULT '1',
		`use_template` VARCHAR(255) NULL DEFAULT '',

		`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();