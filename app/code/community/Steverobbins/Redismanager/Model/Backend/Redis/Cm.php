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

class Steverobbins_Redismanager_Model_Backend_Redis_Cm extends Cm_Cache_Backend_Redis
{
    /**
     * Get redis client
     * 
     * @return Credis_Client
     */
    public function getRedis()
    {
        return $this->_redis;
    }
}