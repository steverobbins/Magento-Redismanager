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

class Steverobbins_Redismanager_Adminhtml_RedismanagerController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Steverobbins_Redismanager_Helper_Data
     */
    protected $_helper;

    protected function _construct()
    {
        parent::_construct();
        $this->_title($this->__('System'))
             ->_title($this->__('Redis Management'));
    }
    
    /**
     * Manager page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/redismanager');
        $this->renderLayout();
    }
    
    /**
     * View keys page
     *
     * @return void
     */
    public function keysAction()
    {
        $this->_title($this->__('View Keys'));
        $this->loadLayout();
        $this->_setActiveMenu('system/redismanager');
        $this->renderLayout();
    }

    /**
     * Flush a Redis DB
     *
     * @return void
     */
    public function flushDbAction()
    {
        $id       = $this->getRequest()->getParam('id');
        $services = $this->_getHelper()->getServices();
        if ($id === false || !isset($services[$id])) {
            Mage::getSingleton('core/session')->addError($this->__('Unable to flush Redis database')); 
        } else {
            $this->_flushDb($services[$id]);
        }
        $this->_redirect('*/*');
    }

    /**
     * Process multiple services
     * 
     * @return void
     */
    public function massAction()
    {
        $services = $this->_getHelper()->getServices();
        $ids      = $this->getRequest()->getPost('service');
        if (count($ids)) {
            foreach ($this->getRequest()->getPost('service') as $id) {
                $this->_flushDb($services[$id]);
            }
        }
        $this->_redirect('*/*');
    }

    /**
     * Flush matching keys
     *
     * @return void
     */
    public function flushByKeyAction()
    {
        $keys       = $this->getRequest()->getPost('redisKeys');
        $clearCount = 0;
        if ($keys) {
            $keys       = explode("\n", $keys);
            $keys       = array_map(array($this, '_prepareKey'), $keys);
            $helper     = $this->_getHelper();
            $services   = $helper->getServices();
            foreach ($services as $service) {
                $redis = $this->_getHelper()->getRedisInstance(
                    $service['host'],
                    $service['port'],
                    $service['password'],
                    $service['db']
                )->getRedis();
                $matched = array();
                foreach ($keys as $key) {
                    $matched = array_merge($matched, $redis->keys($key));
                }
                if (count($matched)) {
                    $clearCount += $redis->del($matched);
                }
            }
        }
        Mage::getSingleton('core/session')->addSuccess($this->__(
            '%s key(s) cleared',
            $clearCount
        ));
        $this->_redirect('*/*');
    }

    /**
     * Flushes all services
     *
     * @return void
     */
    public function flushAllAction()
    {
        $flushThis = $this->getRequest()->getParam('host', null);
        $flushed   = array();
        foreach ($this->_getHelper()->getServices() as $service) {
            $serviceMatch = $service['host'] . ':' . $service['port'];
            if (
                in_array($serviceMatch, $flushed)
                || (!is_null($flushThis) && $flushThis != $serviceMatch)
            ) {
                continue;
            }
            try {
                $this->_getHelper()->getRedisInstance(
                    $service['host'],
                    $service['port'],
                    $service['password'],
                    $service['db']
                )->getRedis()->flushAll();
                $flushed[] = $serviceMatch;
                Mage::getSingleton('core/session')->addSuccess($this->__('%s flushed', $serviceMatch)); 
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage()); 
            }
        }
        $this->_redirect('*/*');
    }

    /**
     * Prepare keys for search
     *
     * @param  string
     * @return string
     */
    protected function _prepareKey($key)
    {
        return '*' . trim($key) . '*';
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('admin/system/redismanager');
    }

    /**
     * Flush a db
     * 
     * @param  array $service
     * @return void
     */
    protected function _flushDb(array $service)
    {
        try {
            $redis = $this->_getHelper()->getRedisInstance(
                $service['host'],
                $service['port'],
                $service['password'],
                $service['db']
            );
            $redis->clean(Zend_Cache::CLEANING_MODE_ALL);
            Mage::getSingleton('core/session')->addSuccess($this->__('%s database flushed', $service['name'])); 
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage()); 
        }
    }

    /**
     * Get helper
     *
     * @return Steverobbins_Redismanager_Helper_Data
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('redismanager');
        }
        return $this->_helper;
    }
}
