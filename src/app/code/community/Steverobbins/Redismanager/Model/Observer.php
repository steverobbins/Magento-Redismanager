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
 * Redis manager observer class
 *
 * @category  Steverobbins
 * @package   Steverobbins_Redismanager
 * @author    Henry van Megen <h.van.megen@gmail.com>
 * @copyright 2018 Henry van Megen
 * @license   http://creativecommons.org/licenses/by/3.0/deed.en_US Creative Commons Attribution 3.0 Unported License
 * @link      https://github.com/steverobbins/Magento-Redismanager
 */
class Steverobbins_Redismanager_Model_Observer
{
    /**
     * Cached helper
     *
     * @var Steverobbins_Redismanager_Helper_Data
     */
    protected $_helper;

    /**
     * Event observer for adminhtml_cache_flush_system event
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function cacheFlushSystem(Varien_Event_Observer $observer)
    {
        try {
            $this->_getHelper()->flushAllByObserver();
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    /**
     * Event observer for adminhtml_cache_flush_all event
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function cacheFlushAll(Varien_Event_Observer $observer)
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
