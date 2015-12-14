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
 * Main block for display configured redis services in admin
 *
 * @category  Steverobbins
 * @package   Steverobbins_Redismanager
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright 2014 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/3.0/deed.en_US Creative Commons Attribution 3.0 Unported License
 * @link      https://github.com/steverobbins/Magento-Redismanager
 */
class Steverobbins_Redismanager_Block_Adminhtml_Manager
    extends Mage_Adminhtml_Block_Template
{
    const DEFAULT_MISSING_STRING = 'N/A';

    /**
     * Cached array of info from a redis instance
     *
     * @var array
     */
    protected $_info;

    /**
     * Build multidimensional array of servers by host:port
     *
     * @return array
     */
    public function getSortedServices()
    {
        $helper = $this->helper('redismanager');
        $sorted = array();
        foreach ($helper->getServices() as $id => $service) {
            $hostPort = $service['host'] . ':' . $service['port'];
            if (!isset($sorted[$hostPort])) {
                $client = $helper->getRedisInstance(
                    $service['host'],
                    $service['port'],
                    $service['password'],
                    $service['db']
                );
                $sorted[$hostPort] = $this->_getSortedService($service, $id, $client);
                continue;
            }
            $client = $helper->getRedisInstance(
                $service['host'],
                $service['port'],
                $service['password'],
                $service['db']
            );
            $sorted[$hostPort]['services'][$id] = array(
                'name' => $service['name'],
                'db' => $service['db'],
                'keys' => count($client->getRedis()->keys('*'))
            );
        }
        return $sorted;
    }

    /**
     * Get a formatted array of data from the redis info
     *
     * @param  array              $service
     * @param  integer            $id
     * @param  Zend_Cache_Backend $client
     *
     * @return array
     */
    protected function _getSortedService(array $service, $id, Zend_Cache_Backend $client)
    {
        $this->_info = $client->getRedis()->info();
        return array(
            'host' => $service['host'],
            'port' => $service['port'],
            'uptime' => $this->_getUptime(),
            'connections' => $this->_getInfo('connected_clients'),
            'memory' => $this->_getMemory(),
            'role' => $this->_getInfo('role') . $this->_getSlaves(),
            'lastsave' => $this->_getLastSave(),
            'services' => array(
                $id => array(
                    'name' => $service['name'],
                    'db' => $service['db'],
                    'keys' => count($client->getRedis()->keys('*'))
                )
            )
        );
    }

    /**
     * Get the uptime for this service
     *
     * @return string
     */
    protected function _getUptime()
    {
        $uptime = $this->_getInfo('uptime_in_seconds', false);
        if (!$uptime) {
            return $this->__(self::DEFAULT_MISSING_STRING);
        }
        return $this->__(
            '%s days, %s hours, %s minutes, %s seconds',
            floor($uptime / 86400),
            floor($uptime / 3600) % 24,
            floor($uptime / 60) % 60,
            floor($uptime % 60)
        );
    }

    /**
     * Get the memory usage
     *
     * @return string
     */
    protected function _getMemory()
    {
        $used = $this->_getInfo('used_memory_human', false);
        $peak = $this->_getInfo('used_memory_peak_human', false);
        if (!$used || !$peak) {
            return $this->__(self::DEFAULT_MISSING_STRING);
        }
        return $used . ' / ' . $peak;
    }

    /**
     * Get any connected slaves
     *
     * @return string
     */
    protected function _getSlaves()
    {
        $slaves = $this->_getInfo('connected_slaves', false);
        if (!$slaves) {
            return '';
        }
        return $this->__(' (%s slaves)', $slaves);
    }

    /**
     * Get the last save timestamp
     *
     * @return string
     */
    protected function _getLastSave()
    {
        $lastSave = $this->_getInfo('rdb_last_save_time', false);
        if (!$lastSave) {
            return $this->__(self::DEFAULT_MISSING_STRING);
        }
        try {
            return $this->helper('core')->formatTime(
                Mage::getSingleton('core/date')->timestamp($lastSave),
                Mage_Core_Model_Locale::FORMAT_TYPE_LONG
            );
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Get information from the redis client
     *
     * @param string $key
     * @param mixed  $ifMissing
     *
     * @return mixed
     */
    protected function _getInfo($key, $ifMissing = self::DEFAULT_MISSING_STRING)
    {
        if (isset($this->_info[$key])) {
            return $this->_info[$key];
        }
        return is_string($ifMissing) ? $this->__($ifMissing) : $ifMissing;
    }
}
