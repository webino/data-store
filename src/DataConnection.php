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

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;

/**
 * Class DataConnection
 * @package data-store
 */
class DataConnection extends Connection implements InstanceFactoryMethodInterface
{
    /**
     * @var AbstractDataConnectionOptions
     */
    protected $options;

    /**
     * @param CreateInstanceEventInterface $event
     * @return DataConnection
     * @throws DBALException
     */
    public static function create(CreateInstanceEventInterface $event): DataConnection
    {
        $params = $event->getParameters();
        $options = [];
        $connOptions = null;
        if (isset($params[0]) && $params[0] instanceof AbstractDataConnectionOptions) {
            /** @var AbstractDataConnectionOptions $connOptions */
            $connOptions = $params[0];
            $options = $connOptions->toArray();
        }
        $options['wrapperClass'] = static::class;
        $config = new Configuration;
        /** @var DataConnection $connection */
        $connection = DriverManager::getConnection($options, $config);
        $connOptions and $connection->setOptions($connOptions);
        return $connection;
    }

    /**
     * @param AbstractDataConnectionOptions $options
     */
    public function setOptions(AbstractDataConnectionOptions $options): void
    {
        $this->options = $options;
    }

    /**
     * Create database.
     *
     * @throws DBALException
     * @return void
     */
    public function setUp(): void
    {
        $options = clone $this->options;
        $options->database = '';

        /** @var DataConnection $connection */
        $connection = DriverManager::getConnection($options->toArray(), $this->getConfiguration());

        $connection->connect();
        $schema = $connection->getSchemaManager();
        $schema->dropAndCreateDatabase($this->getDatabase());
        $connection->close();
    }

    /**
     * Drops database.
     *
     * @return void
     */
    public function tearDown(): void
    {
        $schema = $this->getSchemaManager();
        $schema->dropDatabase($this->getDatabase());
        $this->close();
    }
}
