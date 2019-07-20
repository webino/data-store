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

use PDO;

/**
 * Class MySqlDataStoreConnection
 * @package data-store
 */
class MySqlDataConnectionOptions extends AbstractDataConnectionOptions
{
    /**
     * Database driver.
     *
     * @var string
     */
    public $driver = 'pdo_mysql';

    /**
     * Extra options.
     *
     * @var array
     */
    public $extra = [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\' COLLATE \'utf8_unicode_ci\'',
    ];
}
