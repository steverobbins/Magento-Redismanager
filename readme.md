Magento Redis Manager
==============================

Magento's missing utility for managing Redis services

## Features

1. Automatic or manual configuration in System > Config > Advanced > Redis Caches & Sessions
 * Ideal for when caching services differ in load balanced environments
2. Flush databases directly from Magento's admin panel
 * Though caches can be cleared via native Cache Management, sometimes keys are missed.  There is also no functionality to clear sessions.  Magento Redis Manager gives you this ability!
3. Delete cache keys by matched expression
4. View cache keys
5. View usage statistics


## Screenhots

![Manage Services](http://i.imgur.com/JGlA5xM.png)

![View Keys](http://i.imgur.com/VGjLgGE.png)

## Installation

1. Download the files and copy to your Magento installation folder.
2. Clear Magento caches.

### Installation with Modman

    cd /path/to/magento/
    modman init
    modman clone https://github.com/steverobbins/Magento-Redismanager.git

## License

[Creative Commons Attribution 3.0 Unported License](http://creativecommons.org/licenses/by/3.0/deed.en_US)
