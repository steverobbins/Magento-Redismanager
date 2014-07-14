<?php

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
                // get cache
                $node = $config->getXpath('global/cache[1]');
                if ((string)$node[0]->backend == 'Cm_Cache_Backend_Redis') {
                    $this->_services[] = array(
                        'name'     => $this->__('Cache'),
                        'host'     => (string)$node[0]->backend_options->server,
                        'port'     => (string)$node[0]->backend_options->port,
                        'password' => (string)$node[0]->backend_options->password,
                        'db'       => (string)$node[0]->backend_options->database
                    );
                }
                // get fpc
                $node = $config->getXpath('global/full_page_cache[1]');
                if ((string)$node[0]->backend == 'Cm_Cache_Backend_Redis') {
                    $this->_services[] = array(
                        'name'     => $this->__('Page Cache'),
                        'host'     => (string)$node[0]->backend_options->server,
                        'port'     => (string)$node[0]->backend_options->port,
                        'password' => (string)$node[0]->backend_options->password,
                        'db'       => (string)$node[0]->backend_options->database
                    );
                }
                // get session
                $node = $config->getXpath('global/redis_session');
                if ($node) {
                    $this->_services[] = array(
                        'name'     => $this->__('Session'),
                        'host'     => (string)$node[0]->host,
                        'port'     => (string)$node[0]->port,
                        'password' => (string)$node[0]->password,
                        'db'       => (string)$node[0]->db
                    );
                }
            }
            else {
                $this->_services = unserialize($this->getConfig('manual'));
            }
        }
        return $this->_services;
    }
}