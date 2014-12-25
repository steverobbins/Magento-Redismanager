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

class Steverobbins_Redismanager_Block_Adminhtml_Keys
    extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Steverobbins_Redismanager_Helper_Data
     */
    protected $_helper;

    /**
     * @var array
     */
    protected $_service;

    /**
     * Get all keys for client
     * 
     * @return array
     */
    public function getKeys()
    {
        $service = $this->_getService();
        $keys = $this->_getHelper()->getRedisInstance(
            $service['host'],
            $service['port'],
            $service['password'],
            $service['db']
        )
        ->getRedis()
        ->keys('*');
        sort($keys);
        return $keys;
    }

    /**
     * Get service name
     *
     * @return string
     */
    public function getName()
    {
        $service = $this->_getService();
        return $service['name'];
    }

    /**
     * Get service array
     *
     * @return array
     */
    protected function _getService()
    {
        if (is_null($this->_service)) {
            $id       = $this->getRequest()->getParam('id');
            $services = $this->_getHelper()->getServices();
            if (isset($services[$id])) {
                $this->_service = $services[$id];
            } else {
                $this->_service = array();
            }
        }
        return $this->_service;
    }

    /**
     * Get helper
     *
     * @return Steverobbins_Redismanager_Helper_Data
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = $this->helper('redismanager');
        }
        return $this->_helper;
    }
}
