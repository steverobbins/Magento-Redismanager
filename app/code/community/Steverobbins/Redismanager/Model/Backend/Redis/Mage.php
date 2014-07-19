<?php

class Steverobbins_Redismanager_Model_Backend_Redis_Mage extends Mage_Cache_Backend_Redis
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