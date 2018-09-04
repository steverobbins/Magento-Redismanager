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
 * Redis manager helper class
 *
 * @category  Steverobbins
 * @package   Steverobbins_Redismanager
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright 2014 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/3.0/deed.en_US Creative Commons Attribution 3.0 Unported License
 * @link      https://github.com/steverobbins/Magento-Redismanager
 */
class Steverobbins_Redismanager_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_DEFAULT_SECTION = 'redismanager';
    const XML_PATH_DEFAULT_GROUP   = 'settings';


    /**
     * Services cache
     *
     * @var array
     */
    protected $_services;

    /**
     * Config getter
     *
     * @param  string $path
     * @return string
     */
    public function getConfig($path)
    {
        $bits  = explode('/', $path);
        $count = count($bits);
        return Mage::getStoreConfig(
            ($count == 3 ? $bits[0] : self::XML_PATH_DEFAULT_SECTION) . '/' .
            ($count > 1 ? $bits[$count - 2] : self::XML_PATH_DEFAULT_GROUP) . '/' .
            $bits[$count - 1]
        );
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
                foreach (array('cache', 'full_page_cache', 'fpc') as $cacheType) {
                    $node = $config->getXpath('global/' . $cacheType . '[1]');
                    if (isset($node[0]->backend) && in_array((string)$node[0]->backend, array('Cm_Cache_Backend_Redis', 'Mage_Cache_Backend_Redis'))) {
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
            } else {
                $this->_services = unserialize($this->getConfig('manual'));
            }
        }
        return $this->_services;
    }

    /**
     * Perform a flushAll when synchflush is enabled (for use in the event observers)
     *
     * @return void
     */
    public function flushAllByObserver()
    {
        if ($this->getConfig('syncflush')) {
            Mage::getSingleton('core/session')
                ->addSuccess('Redismanager has observed a cache flush by Magento, flushing Redis...');
            $this->flushAll();
        }
    }

    /**
     * Flush all Redis caches
     *
     * @param string $flushThis
     * @return void
     */
    public function flushAll($flushThis = null)
    {
        $flushed = array();
        foreach ($this->getServices() as $service) {
            $serviceMatch = $service['host'] . ':' . $service['port'];
            if (in_array($serviceMatch, $flushed)
                || (!is_null($flushThis) && $flushThis != $serviceMatch)
            ) {
                continue;
            }
            try {
                $this->getRedisInstance(
                    $service['host'],
                    $service['port'],
                    $service['password'],
                    $service['db']
                )->getRedis()->flushAll();
                $flushed[] = $serviceMatch;
                $serviceName = $service['name'] . ' (' . $service['host'] . ':' . $service['port'] . ')';
                Mage::getSingleton('core/session')->addSuccess($this->__('%s flushed', $serviceName));
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
        return $flushed;
    }

    /**
     * Assign values with casting
     *
     * @param  string $name
     * @param  string $host
     * @param  string $port
     * @param  string $pass
     * @param  string $db
     *
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
     *
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
        } elseif (class_exists('Cm_Cache_Backend_Redis')) {
            return Mage::getModel('redismanager/backend_redis_cm', array(
                'server'   => $host,
                'port'     => $port,
                'password' => $pass,
                'database' => $db
            ));
        }
        Mage::throwException('Unable to determine Redis backend class.');
    }
}
