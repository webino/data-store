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

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Table;

/**
 * Class AbstractTableDataStore
 * @package data-store
 */
abstract class AbstractTableDataStore implements InstanceFactoryMethodInterface
{
    public const NAME = 'example';

    public const COLUMNS = [];

    /**
     * @var DataConnection
     */
    protected $connection;

    /**
     * @param CreateInstanceEventInterface $event
     * @return AbstractTableDataStore
     */
    public static function create(CreateInstanceEventInterface $event): AbstractTableDataStore
    {
        $params = $event->getParameters();
        return new static(...$params);
    }

    /**
     * @param DataConnection $connection
     */
    public function __construct(DataConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return void
     * @throws DBALException
     */
    public function setUp(): void
    {
        $schema = $this->connection->getSchemaManager();

        $table = new Table($this::NAME);

        $table->addColumn('id', 'integer')
            ->setAutoincrement(true)
            ->setUnsigned(true);

        foreach ($this::COLUMNS as $colName => $colSpec) {
            $colOpts = $colSpec;
            unset($colOpts['type']);
            $table->addColumn($colName, $colSpec['type'], $colOpts);
        }

        $table->setPrimaryKey(['id']);

        $schema->createTable($table);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        $schema = $this->connection->getSchemaManager();
        $schema->dropTable($this::NAME);
    }

    /**
     * @param array $data
     */
    public function insert(array $data): void
    {
        $query = $this->connection->createQueryBuilder();
        $values = [];
        foreach ($data as $key => $value) {
            $values[$key] = '?';
        }

        $params = array_values($data);

        $query->insert($this::NAME)->values($values)->setParameters($params);
        $query->execute();
    }
}
