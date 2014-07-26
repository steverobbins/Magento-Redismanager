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

class Steverobbins_Redismanager_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_DEFAULT_SECTION = 'redismanager';
    const XML_PATH_DEFAULT_GROUP   = 'settings';

    /**
     * Config cache
     * @var array
     */
    private $_config = array();

    /**
     * Services cache
     * @var array
     */
    private $_services;

    /**
     * Config getter
     * 
     * @param  string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        if (!isset($this->_config[$path])) {
            $bits  = explode('/', $path);
            $count = count($bits);
            $this->_config[$path] = Mage::getStoreConfig(
                ($count == 3 ? $bits[0] : self::XML_PATH_DEFAULT_SECTION) . '/' .
                ($count > 1 ? $bits[$count - 2] : self::XML_PATH_DEFAULT_GROUP) . '/' .
                $bits[$count - 1]
            );
        }
        return $this->_config[$path];
    }

    /**
     * Fetch all redis services
     *
     * @return array
     */
    public function getServices()
    {
        if (is_null($this->_services)) {
            if ($this->getConfig('auto')) {
                $this->_services = array();
                $config = Mage::app()->getConfig();
                foreach (array('cache', 'full_page_cache') as $cacheType) {
                    $node = $config->getXpath('global/' . $cacheType . '[1]');
                    if (isset($node[0]->backend) && in_array((string)$node[0]->backend, array(
                        'Cm_Cache_Backend_Redis',
                        'Mage_Cache_Backend_Redis'
                    ))) {
                        $this->_services[] = $this->_buildServiceArray(
                            $this->__(str_replace('_', ' ', uc_words($cacheType))),
                            $node[0]->backend_options->server,
                            $node[0]->backend_options->port,
                            $node[0]->backend_options->password,
                            $node[0]->backend_options->database
                        );
                    }
                }
                // get session
                $node = $config->getXpath('global/redis_session');
                if ($node) {
                    $this->_services[] = $this->_buildServiceArray(
                        $this->__('Session'),
                        $node[0]->host,
                        $node[0]->port,
                        $node[0]->password,
                        $node[0]->db
                    );
                }
            }
            else {
                $this->_services = unserialize($this->getConfig('manual'));
            }
        }
        return $this->_services;
    }

    /**
     * Assign values with casting
     * 
     * @param  string $name
     * @param  string $host
     * @param  string $port
     * @param  string $pass
     * @param  string $db
     * @return array
     */
    protected function _buildServiceArray($name, $host, $port, $pass, $db)
    {
        return array(
            'name'     => $name,
            'host'     => (string)$host,
            'port'     => (string)$port,
            'password' => (string)$pass,
            'db'       => (string)$db
        );
    }
    
    /**
     * Get Redis client
     * 
     * @param  string $host
     * @param  string $port
     * @param  string $pass
     * @param  string $db
     * @return Steverobbins_Redismanager_Model_Backend_Redis_Cm|Steverobbins_Redismanager_Model_Backend_Redis_Mage
     */
    public function getRedisInstance($host, $port, $pass, $db)
    {
        if (class_exists('Mage_Cache_Backend_Redis')) {
            return Mage::getModel('redismanager/backend_redis_mage', array(
                'server'   => $host,
                'port'     => $port,
                'password' => $pass,
                'database' => $db
            ));
        } else if (class_exists('Cm_Cache_Backend_Redis')) {
            return Mage::getModel('redismanager/backend_redis_cm', array(
                'server'   => $host,
                'port'     => $port,
                'password' => $pass,
                'database' => $db
            ));
        }
        return false;
    }
}