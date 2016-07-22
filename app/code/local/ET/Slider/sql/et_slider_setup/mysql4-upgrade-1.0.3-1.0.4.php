<?php

$installer = $this;

$installer->startSetup();

$group_table = $this->getTable('slider/group');
$installer->run("
CREATE TABLE IF NOT EXISTS {$group_table}(
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`description` TEXT NULL DEFAULT '',
	`state` SMALLINT(6) NOT NULL DEFAULT '1',
	
	`css_id` VARCHAR(255) NOT NULL DEFAULT '',
	`css_position` VARCHAR(255) NOT NULL DEFAULT 'relative',
	`css_width` VARCHAR(255) NOT NULL DEFAULT '',
	`css_height` VARCHAR(255) NOT NULL DEFAULT '',
	
	`loading_screen` SMALLINT(6) NOT NULL DEFAULT '1',
	
	`fill_mode` SMALLINT(6) NOT NULL DEFAULT '0',
	`lazy_loading` SMALLINT(6) NOT NULL DEFAULT '1',
	`start_index` INT(10) NOT NULL DEFAULT '0',
	`auto_play` SMALLINT(6) NOT NULL DEFAULT '1',
	`loop` SMALLINT(6) NOT NULL DEFAULT '1',
	`hwa` SMALLINT(6) NOT NULL DEFAULT '1',
	`auto_play_steps` INT(10) NOT NULL DEFAULT '1',
	`auto_play_interval` INT(10) NOT NULL DEFAULT '3000',
	`pause_on_hover` SMALLINT(6) NOT NULL DEFAULT '3',
	`arrow_key_navigation` SMALLINT(6) NOT NULL DEFAULT '0',
	`slide_duration` INT(10) NOT NULL DEFAULT '500',
	`slide_easing` VARCHAR(255) NOT NULL DEFAULT '".'$JssorEasing$.$EaseOutQuad'."',
	`min_drag_offset_to_slide` INT(10) NOT NULL DEFAULT '20',
	`slide_width` INT(10) NOT NULL DEFAULT '0',
	`slide_height` INT(10) NOT NULL DEFAULT '0',
	`slide_spacing` INT(10) NOT NULL DEFAULT '0',
	`display_pieces` INT(10) NOT NULL DEFAULT '1',
	`parking_position` INT(10) NOT NULL DEFAULT '0',
	`ui_search_mode` SMALLINT(6) NOT NULL DEFAULT '1',
	`play_orientation` SMALLINT(6) NOT NULL DEFAULT '1',
	`drag_orientation` SMALLINT(6) NOT NULL DEFAULT '1',
	
	-- BulletNavigatorOptions
	`bullet_class` VARCHAR(255) NOT NULL DEFAULT '".'$JssorBulletNavigator$'."',
	`bullet_chance_to_show` SMALLINT(6) NOT NULL DEFAULT '2',
	`bullet_action_mode` SMALLINT(6) NOT NULL DEFAULT '1',
	`bullet_auto_center` SMALLINT(6) NOT NULL DEFAULT '0',
	`bullet_steps` INT(10) NOT NULL DEFAULT '1',
	`bullet_lanes` INT(10) NOT NULL DEFAULT '1',
	`bullet_spacing_x` INT(10) NOT NULL DEFAULT '0',
	`bullet_spacing_y` INT(10) NOT NULL DEFAULT '0',
	`bullet_orientation` INT(10) NOT NULL DEFAULT '1',
	
	`arrow_class` VARCHAR(255) NOT NULL DEFAULT '".'$JssorArrowNavigator$'."',
	`arrow_chance_to_show` SMALLINT(6) NOT NULL DEFAULT '2',
	`arrow_steps` INT(10) NOT NULL DEFAULT '1',
	
	`thumbnail_class` VARCHAR(255) NOT NULL DEFAULT '".'$JssorThumbnailNavigator$'."',
	`thumbnail_change_to_show` SMALLINT(6) NOT NULL DEFAULT '2',
	`thumbnail_loop` SMALLINT(6) NOT NULL DEFAULT '1',
	`thumbnail_action_mode` SMALLINT(6) NOT NULL DEFAULT '1',
	`thumbnail_auto_center` SMALLINT(6) NOT NULL DEFAULT '3',
	`thumbnail_lanes` INT(10) NOT NULL DEFAULT '1',
	`thumbnail_spacing_x` INT(10) NOT NULL DEFAULT '0',
	`thumbnail_spacing_y` INT(10) NOT NULL DEFAULT '0',
	`thumbnail_display_pieces` SMALLINT(6) NOT NULL DEFAULT '1',
	`thumbnail_parking_position` SMALLINT(6) NOT NULL DEFAULT '0',
	`thumbnail_orientation` SMALLINT(6) NOT NULL DEFAULT '1',
	`thumbnail_disable_drag` SMALLINT(6) NOT NULL DEFAULT '0',
	
	`slideshow_class` VARCHAR(255) NOT NULL DEFAULT '".'$JssorSlideshowRunner$'."',
	`slideshow_transitions` VARCHAR(5120) NOT NULL DEFAULT '',
	`slideshow_transitions_order` SMALLINT(6) NOT NULL DEFAULT '1',
	`slideshow_show_link` SMALLINT(6) NOT NULL DEFAULT '0',
	
	`caption_class` VARCHAR(255) NOT NULL DEFAULT '".'$JssorCaptionSlider$'."',
	`caption_transitions` VARCHAR(5120) NOT NULL DEFAULT '',
	`caption_playin_mode` SMALLINT(6) NOT NULL DEFAULT '1',
	`caption_playout_mode` SMALLINT(6) NOT NULL DEFAULT '1',
	
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$slide_table = $this->getTable('slider/slide');
$installer->run("
CREATE TABLE IF NOT EXISTS {$slide_table}(
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`description` TEXT NULL DEFAULT '',
	
	`group_id` INT(11) NULL,
	`state` SMALLINT(6) NOT NULL DEFAULT '1',
	
	`slide_image` VARCHAR(255) NOT NULL DEFAULT '',
	`slide_image_lazyload` SMALLINT(6) NOT NULL DEFAULT '0',
	`slide_thumbnail` VARCHAR(255) NOT NULL DEFAULT '',
	`slide_transition` VARCHAR(2550) NOT NULL DEFAULT '',
	
	`css_position` VARCHAR(255) NOT NULL DEFAULT 'absolute',
	`css_top` VARCHAR(255) NOT NULL DEFAULT '0',
	`css_right` VARCHAR(255) NOT NULL DEFAULT '0',
	`css_bottom` VARCHAR(255) NOT NULL DEFAULT '0',
	`css_left` VARCHAR(255) NOT NULL DEFAULT '0',
	`css_width` VARCHAR(255) NOT NULL DEFAULT '',
	`css_height` VARCHAR(255) NOT NULL DEFAULT '',
	`css_overflow` VARCHAR(255) NOT NULL DEFAULT '',
	`css_cursor` VARCHAR(255) NOT NULL DEFAULT '',
	
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$caption_table = $this->getTable('slider/caption');
$installer->run("
CREATE TABLE IF NOT EXISTS {$caption_table}(
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`state` SMALLINT(6) NOT NULL DEFAULT '1',
	
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`description` TEXT NULL DEFAULT '',
	`caption_image` VARCHAR(512) NOT NULL DEFAULT '',
	`slide_id` INT(11) NULL,
	
	`play_in` VARCHAR(255) NOT NULL DEFAULT '',
	`play_out` VARCHAR(255) NOT NULL DEFAULT '',
	`play_in_dur` INT(11) NULL,
	`play_out_dur` INT(11) NULL,
	`delay` INT(11) NULL,
	
	`css_classname` VARCHAR(255) NOT NULL DEFAULT '',
	`inline_style` SMALLINT(6) NOT NULL DEFAULT 1,
	`css_position` VARCHAR(255) NOT NULL DEFAULT 'absolute',
	`css_top` VARCHAR(255) NOT NULL DEFAULT '',
	`css_left` VARCHAR(255) NOT NULL DEFAULT '',
	`css_width` VARCHAR(255) NOT NULL DEFAULT '',
	`css_height` VARCHAR(255) NOT NULL DEFAULT '',
	`css_overflow` VARCHAR(255) NOT NULL DEFAULT 'hidden',
	
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();