<?php

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