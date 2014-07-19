<?php

class Steverobbins_Redismanager_Adminhtml_RedismanagerController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Steverobbins_Redismanager_Helper_Data
     */
    protected $_helper;
    
    /**
     * Manager page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Redis Manager'));
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
        $helper   = $this->_getHelper();
        $services = $helper->getServices();
        $service  = $services[$id];
        if ($id === false || !isset($services[$id])) {
            Mage::getSingleton('core/session')->addError($this->__('Unable to flush Redis database')); 
        } else {
            $this->_flushDb($service);
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
        $helper   = $this->_getHelper();
        $services = $helper->getServices();
        foreach ($this->getRequest()->getPost('service') as $id) {
            $this->_flushDb($services[$id]);
        }
        $this->_redirect('*/*');
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
            $redis->clean();
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
