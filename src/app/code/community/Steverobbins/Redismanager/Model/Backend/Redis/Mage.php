<?php
/**
 * Redis Management Module
 *
 * PHP Version 5
 *
 * @category  Steverobbins
 * @package   Steverobbins_Redismanager
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright 2014 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/3.0/deed.en_US Creative Commons Attribution 3.0 Unported License
 * @link      https://github.com/steverobbins/Magento-Redismanager
 */

/**
 * Redis client wrapper for Mage
 *
 * @category  Steverobbins
 * @package   Steverobbins_Redismanager
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright 2014 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/3.0/deed.en_US Creative Commons Attribution 3.0 Unported License
 * @link      https://github.com/steverobbins/Magento-Redismanager
 */
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
