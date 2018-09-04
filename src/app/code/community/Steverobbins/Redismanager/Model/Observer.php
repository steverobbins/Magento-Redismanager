<?php

class Steverobbins_Redismanager_Model_Observer
{
    /**
     * Cached helper
     *
     * @var Steverobbins_Redismanager_Helper_Data
     */
    protected $_helper;

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function adminhtml_cache_flush_system(Varien_Event_Observer $observer)
    {
        try {
            $this->_getHelper()->flushAllByObserver();
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function adminhtml_cache_flush_all(Varien_Event_Observer $observer)
    {
        try {
            $this->_getHelper()->flushAllByObserver();
        } catch (Exception $e) {
            Mage::logException($e);
        } 
        return $this;
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