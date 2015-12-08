[![Master Build Status](https://img.shields.io/travis/steverobbins/Magento-Redismanager/master.svg?style=flat-square)](https://travis-ci.org/steverobbins/Magento-Redismanager)
[![Master Code Quality](https://img.shields.io/scrutinizer/g/steverobbins/Magento-Redismanager/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/steverobbins/Magento-Redismanager/?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/steverobbins/redismanager.svg?style=flat-square)](https://packagist.org/packages/steverobbins/redismanager)

Magento Redis Manager
==============================

[![Join the chat at https://gitter.im/steverobbins/Magento-Redismanager](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/steverobbins/Magento-Redismanager?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Utility for managing Redis services

# Features

1. Automatic or manual configuration in **System > Config > Advanced > Redis Management**
 * Ideal for when caching services differ in load balanced environments
2. Flush databases directly from Magento's admin panel in **System > Redis Management**
 * Though some caches can be cleared via native Cache Management, Magento only deletes certain caches based on key or tag.  There is also no method to clear sessions.  This module gives the admin the ability to execute `flushdb` and `flushall` commands for true cache purging.
3. Delete cache keys by matched strings
4. Auto-refresh redis dashboard every X seconds
5. View usage statistics


# Screenshots

![Manage Services](http://i.imgur.com/OQnxqiY.png)

# Installation

1. Copy the contents of src/ to your Magento installation
2. Clear Magento caches
3. Log out of admin

## Installation with Modman

    cd /path/to/magento/
    modman init
    modman clone https://github.com/steverobbins/Magento-Redismanager.git

# FAQ

* What caches are supported?
  * The module will automatically try and detect Redis settings for the following services:
    * Cache
    * Session
    * Enterprise Full Page Cache
    * Lesti FPC
* Can I configure new Redis services with the module?
  * Negative.  This module is only used to monitor and flush already configured services.  To add/change your services you need to modify app/etc/local.xml, enterprise.xml, fpc.xml ,etc.
* I keep seeing a `Connection to Redis failed` error.  What does this mean?
  * You may have incorrectly configured your settings.  Check you settings in System > Config > Advanced > Redis Management.  Double check your Manual Configuration or try changing `Automatically detect Redis services` to `Yes`
* What versions of Magento are supported?
  * This module has been tested on CE 1.7, 1.8, 1.9 and EE 1.12, 1.13, 1.14, however it should work with any version.

# Support

Please submit any issues or feature requests to the [issue tracker](https://github.com/steverobbins/Magento-Redismanager/issues).

# License

[Creative Commons Attribution 3.0 Unported License](http://creativecommons.org/licenses/by/3.0/deed.en_US)
