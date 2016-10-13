# Ride: Beanstalkd Queue Library

Beanstalkd implementation for the queue library of the PHP Ride framework.

This implementation could not implement an overview of the waiting queue jobs nor updating the status since Beanstalkd does not support this.

## Code Sample

Check this code sample to see how to initialize this library:

```php
<?php

use ride\library\queue\BeanstalkdFactory;
use ride\library\queue\BeanstalkdQueueManager;

function createQueueManager() {
    $factory = new BeanstalkdFactory();
    
    $client = $factory->createClient('localhost', 11300);
    
    $queueManager = new BeanstalkdQueueManager($client);
    
    return $queueManager;
}
```

### Related Modules

You can check the following related modules to this library:

- [ride/app-queue-beanstalkd](https://github.com/all-ride/ride-app-queue-beanstalkd)
- [ride/app-queue-orm](https://github.com/all-ride/ride-app-queue-orm)
- [ride/cli-queue](https://github.com/all-ride/ride-cli-queue)
- [ride/lib-common](https://github.com/all-ride/ride-lib-common)
- [ride/lib-log](https://github.com/all-ride/ride-lib-log)
- [ride/lib-queue](https://github.com/all-ride/ride-lib-queue)
- [ride/wba-queue](https://github.com/all-ride/ride-wba-queue)
- [ride/wra-queue](https://github.com/all-ride/ride-wra-queue)

## Installation

You can use [Composer](http://getcomposer.org) to install this library.

```
composer require ride/lib-queue
```
