<?php

namespace App\Connectors;

use phpseclib\Net\SSH2;

final class SSHConnector {

    /**
     * @var SSH2
     */
    private $connection;

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     */
    public function __construct(string $host, string $username, string $password) {
        $ssh = new SSH2($host);
        if (!$ssh->login($username, $password)) {
            exit('Login Failed');
        }

        $this->connection = $ssh;
    }

    /**
     * @return SSH2
     */
    public function getConnection(): SSH2 {
        return $this->connection;
    }
}