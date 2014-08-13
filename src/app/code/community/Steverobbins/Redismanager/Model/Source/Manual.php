<?php
/**
 * Redis Management Module
 * 
 * @category   Steverobbins
 * @package    Steverobbins_Redismanager
 * @author     Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright  Copyright (c) 2014 Steve Robbins (https://github.com/steverobbins)
 * @license    http://creativecommons.org/licenses/by/3.0/deed.en_US Creative Commons Attribution 3.0 Unported License
 */

class Steverobbins_Redismanager_Model_Source_Manual
    extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{
    /**
     * Event prefix for observers
     * @var string
     */
    protected $_eventPrefix = 'redismanager_manual';
}
