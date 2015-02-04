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
                $sorted[$hostPort] = array(
                    'host' => $service['host'],
                    'port' => $service['port'],
                    'info' => $info,                    
                    'services' => array(
                        $id => array(
                            'name' => $service['name'],
                            'db'   => $service['db'],
                            'keys' => count($client->getRedis()->keys('*'))
                        )
                    )
                );
                /*$this->__(
                        '%s day(s) %02d:%02d:%02d',
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
                );*/
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
                'db'   => $service['db'],
                'keys' => count($client->getRedis()->keys('*'))
            );
        }
        return $sorted;
    }
}
