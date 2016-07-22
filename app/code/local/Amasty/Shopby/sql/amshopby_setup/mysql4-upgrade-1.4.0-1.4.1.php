<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/value|cms_block:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/value')}` ADD `cms_block` VARCHAR(255);
"); 

$this->endSetup();