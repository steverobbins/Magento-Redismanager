Magento Redis Manager
==============================

Magento's missing utility for managing Redis services

## Features

Automatic or manual configuration in System > Config > Advanced > Redis Caches & Sessions

Ideal for when caching services differ from server to server.

![Manual Configuration](http://i.imgur.com/Xxj7cTp.png)

Flush databases directly from Magento's admin panel

Though caches can be cleared via native Cache Management, sometimes keys are missed.  There is also no functionality to clear sessions.  Magento Redis Manager gives you this ability.

![Clear DBs](http://i.imgur.com/Lq8aOYo.png)

Usage Statistics

![Usage Statistics](http://i.imgur.com/YNdTjOy.png)

## Installation

1. Download the files and copy to your Magento installation folder.
2. Clear Magento caches.

### Installation with Modman

    cd /path/to/magento/
    modman init
    modman clone https://github.com/steverobbins/Magento-Redismanager.git

## License

[Creative Commons Attribution 3.0 Unported License](http://creativecommons.org/licenses/by/3.0/deed.en_US)
