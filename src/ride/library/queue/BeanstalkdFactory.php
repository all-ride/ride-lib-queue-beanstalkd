<?php

namespace ride\library\queue;

use Beanstalk\Client;

/**
 * Factory to create the Beanstalk client
 */
class BeanstalkdFactory {

    /**
     * Creates a Beanstalk client
     * @param string $host Hostname or IP address of the server
     * @param string $port Port of the server
     * @return \Beanstalk\Client
     */
    public function createClient($host, $port) {
        $config = array(
            'host' => $host,
            'port' => $port,
        );

        $client = new Client($config);
        $client->connect();

        return $client;
    }

}
