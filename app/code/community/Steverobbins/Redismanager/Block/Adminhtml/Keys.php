<?php

class Steverobbins_Redismanager_Block_Adminhtml_Keys
    extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Steverobbins_Redismanager_Helper_Data
     */
    protected $_helper;

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
        $id       = $this->getRequest()->getParam('id');
        $services = $this->_getHelper()->getServices();
        if (isset($services[$id])) {
            return $services[$id];
        }
        return false;
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