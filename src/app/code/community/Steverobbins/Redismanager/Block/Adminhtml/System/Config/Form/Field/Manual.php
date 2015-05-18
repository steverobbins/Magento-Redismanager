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
 * Renderer for manual system config
 *
 * @category  Steverobbins
 * @package   Steverobbins_Redismanager
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright 2014 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/3.0/deed.en_US Creative Commons Attribution 3.0 Unported License
 * @link      https://github.com/steverobbins/Magento-Redismanager
 */
class Steverobbins_Redismanager_Block_Adminhtml_System_Config_Form_Field_Manual
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Add columns
     */
    public function __construct()
    {
        $this->addColumn('name', array(
            'label' => Mage::helper('adminhtml')->__('Name'),
            'style' => 'width:auto'
        ));
        $this->addColumn('host', array(
            'label' => Mage::helper('adminhtml')->__('Host'),
            'style' => 'width:auto'
        ));
        $this->addColumn('port', array(
            'label' => Mage::helper('adminhtml')->__('Port'),
            'style' => 'width:auto'
        ));
        $this->addColumn('password', array(
            'label' => Mage::helper('adminhtml')->__('Password'),
            'style' => 'width:auto'
        ));
        $this->addColumn('db', array(
            'label' => Mage::helper('adminhtml')->__('Database'),
            'style' => 'width:auto'
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add');
        parent::__construct();
    }
}
