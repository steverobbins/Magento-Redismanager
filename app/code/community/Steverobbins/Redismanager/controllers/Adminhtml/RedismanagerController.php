<?php

class Steverobbins_Redismanager_Adminhtml_RedismanagerController
    extends Mage_Adminhtml_Controller_Action
{
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
     * Clean a Redis DB
     *
     * @return void
     */
    public function cleanAction()
    {
        $id = $this->getRequest()->getParam('id');
        $services = Mage::helper('redismanager')->getServices();
        if ($id === false || !isset($services[$id])) {
            Mage::getSingleton('core/session')->addError($this->__('Unable to clear Redis service')); 
        }
        else {
            $service = $services[$id];
            try {
                $redis = new Cm_Cache_Backend_Redis(array(
                    'server'   => $service['host'],
                    'port'     => $service['port'],
                    'password' => $service['password'],
                    'database' => $service['db']
                ));
                $redis->clean();
                Mage::getSingleton('core/session')->addSuccess($this->__('%s service cleared', $service['name'])); 
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage()); 
            }
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
}
