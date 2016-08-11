# Ride: Beanstalkd Queue Library

Beanstalkd implementation for the queue library of the PHP Ride framework.

This implementation could not implement an overview of the waiting queue jobs nor updating the status since Beanstalkd does not support this.

## Code Sample

Check this code sample to see how to initialize this library:

```php
<?php

use ride\library\queue\BeanstalkdFactory;
use ride\library\queue\BeanstalkdQueueManager;

$factory = new BeanstalkdFactory();
$client = $factory->createClient('localhost', 11300);
$queueManager = new BeanstalkdQueueManager($client);
```
