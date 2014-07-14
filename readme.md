Magento Redis Manager
==============================

Magento's missing utility for managing Redis services

## Features

Automatic or manual configuration in System > Config > Advances > Redis Caches & Sessions

![Manual Configuration](http://i.imgur.com/Xxj7cTp.png)

Flush databases directly from Magento's admin panel

![Clear DBs](http://i.imgur.com/EnifvY0.png)

## Installation

1. Download the files and copy to your Magento installation folder.
2. Clear Magento caches.

### Installation with Modman

    cd /path/to/magento/
    modman init
    modman clone https://github.com/steverobbins/Magento-Redismanager.git
