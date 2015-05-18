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
 * Main block for display configured redis services in admin
 *
 * @category  Steverobbins
 * @package   Steverobbins_Redismanager
 * @author    Steve Robbins <steven.j.robbins@gmail.com>
 * @copyright 2014 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/3.0/deed.en_US Creative Commons Attribution 3.0 Unported License
 * @link      https://github.com/steverobbins/Magento-Redismanager
 */
class Steverobbins_Redismanager_Block_Adminhtml_Manager
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Build multidimensional array of servers by host:port
     *
     * @return array
     */
    public function getSortedServices()
    {
        $helper     = $this->helper('redismanager');
        $coreHelper = $this->helper('core');
        $date       = Mage::getSingleton('core/date');
        $sorted     = array();
        foreach ($helper->getServices() as $id => $service) {
            $hostPort = $service['host'] . ':' . $service['port'];
            if (!isset($sorted[$hostPort])) {
                $client = $helper->getRedisInstance(
                    $service['host'],
                    $service['port'],
                    $service['password'],
                    $service['db']
                );
                $info = $client->getRedis()->info();
                $uptime = $info['uptime_in_seconds'];
                $sorted[$hostPort] = array(
                    'host' => $service['host'],
                    'port' => $service['port'],
                    'uptime' => $this->__(
                        '%s days, %s hours, %s minutes, %s seconds',
                        floor($uptime / 86400),
                        floor($uptime / 3600) % 24,
                        floor($uptime / 60) % 60,
                        floor($uptime % 60)
                    ),
                    'connections' => $info['connected_clients'],
                    'memory' => $info['used_memory_human'] . ' / ' . $info['used_memory_peak_human'],
                    'role' => $info['role'] . (
                        (int)$info['connected_slaves'] > 0
                        ? ' (' . $info['connected_slaves'] . ' slaves)'
                        : ''
                    ),
                    'lastsave' => isset($info['rdb_last_save_time'])
                        ? $coreHelper->formatTime(
                            $date->timestamp($info['rdb_last_save_time']),
                            Mage_Core_Model_Locale::FORMAT_TYPE_LONG
                        )
                        : $this->__('N/A'),
                    'services' => array(
                        $id => array(
                            'name' => $service['name'],
                            'db' => $service['db'],
                            'keys' => count($client->getRedis()->keys('*'))
                        )
                    )
                );
                continue;
            }
            $client = $helper->getRedisInstance(
                $service['host'],
                $service['port'],
                $service['password'],
                $service['db']
            );
            $sorted[$hostPort]['services'][$id] = array(
                'name' => $service['name'],
                'db' => $service['db'],
                'keys' => count($client->getRedis()->keys('*'))
            );
        }
        return $sorted;
    }
}
