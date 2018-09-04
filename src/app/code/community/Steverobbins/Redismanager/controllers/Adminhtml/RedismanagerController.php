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
 * Handle view of configured redis services
 *
 * @category  Steverobbins
 * @package   Steverobbins_Redismanager
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright 2014 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/3.0/deed.en_US Creative Commons Attribution 3.0 Unported License
 * @link      https://github.com/steverobbins/Magento-Redismanager
 */
class Steverobbins_Redismanager_Adminhtml_RedismanagerController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Cached helper
     *
     * @var Steverobbins_Redismanager_Helper_Data
     */
    protected $_helper;

    /**
     * Set title
     *
     * @return void
     */
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
     * Bring in grid HTML with ajax
     *
     * @return void
     */
    public function gridAction()
    {
        $this->loadLayout()
            ->renderLayout();
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
                    if ($key !== false) {
                        $matched = array_merge($matched, $redis->keys($key));
                    }
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
        if (is_array($this->_getHelper()->flushAll($flushThis))) {
            $this->_redirect('*/*');
        }
    }

    /**
     * Prepare keys for search
     *
     * @param  string $key
     * @return boolean|string
     */
    protected function _prepareKey($key)
    {
        $key = trim($key);
        if (empty($key)) {
            return false;
        }
        return '*' . $key . '*';
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
            Mage::getSingleton('core/session')->addSuccess($this->__('%s database flushed.', $service['name']));
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
