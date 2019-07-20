<?php
/**
 * Webino™ (http://webino.sk)
 *
 * @link        https://github.com/webino/data-store
 * @copyright   Copyright (c) 2019 Webino, s.r.o. (http://webino.sk)
 * @author      Peter Bačinský <peter@bacinsky.sk>
 * @license     BSD-3-Clause
 */

namespace Webino;

/**
 * Class AbstractDataConnectionOptions
 * @package data-store
 */
abstract class AbstractDataConnectionOptions
{
    /**
     * Database name.
     *
     * @var string
     */
    public $database = '';

    /**
     * Database user.
     *
     * @var string
     */
    public $user = '';

    /**
     * Database password.
     *
     * @var string
     */
    public $password = '';

    /**
     * Database host.
     *
     * @var string
     */
    public $host = 'localhost';

    /**
     * Database port.
     *
     * @var string
     */
    public $port = '3306';

    /**
     * Database driver.
     *
     * @var string
     */
    public $driver = '';

    /**
     * Database charset.
     *
     * @var string
     */
    public $charset = 'UTF8';

    /**
     * Extra options.
     *
     * @var array
     */
    public $extra = [];

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'dbname' => $this->database,
            'user' => $this->user,
            'password' => $this->password,
            'host' => $this->host,
            'port' => $this->port,
            'driver' => $this->driver,
            'charset' => $this->charset,
            'options' => $this->extra,
        ];
    }
}
