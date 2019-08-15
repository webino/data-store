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
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;

/**
 * Class AbstractTableDataStore
 * @package data-store
 */
abstract class AbstractTableDataStore extends AbstractDataStore implements InstanceFactoryMethodInterface
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
     * @return DataConnection
     */
    public function getConnection(): DataConnection
    {
        return $this->connection;
    }

    /**
     * @param string $storeClass
     * @return array
     * @throws DBALException
     * @todo decouple
     */
    protected function createSchema(string $storeClass): array
    {
        $comparator = new Comparator;

        $tables = [];
        $alters = [];

        $name = constant($storeClass . '::NAME');
        $table = new Table($name);

        $tables[$name] = $table;

        $table->addColumn('id', 'integer')
            ->setAutoincrement(true)
            ->setUnsigned(true);

        $columns = constant($storeClass . '::COLUMNS');
        foreach ($columns as $colName => $colSpec) {

            // TODO relations
            if (is_string($colSpec) && class_exists($colSpec)) {
                $subName = constant($colSpec . '::NAME');
                list($subTable) = $this->createSchema($colSpec);
                $subTable = current($subTable);
                $subTableNew = clone $subTable;

                $subIdName = $colName . '_id';
                $subTableNew->addColumn($subIdName, 'integer')
                    ->setUnsigned(true);

                $subTableNew->addForeignKeyConstraint($name, [$subIdName], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE']);

                $subDiff = $comparator->diffTable($subTable, $subTableNew);

                $tables[$subName] = $subTable;
                $alters[] = $subDiff;
                continue;
            }

            // common
            if (is_array($colSpec)) {
                $colOpts = $colSpec;
                unset($colOpts['type']);
                $table->addColumn($colName, $colSpec['type'], $colOpts);
            }
        }

        $table->setPrimaryKey(['id']);
        return [$tables, $alters];
    }

    /**
     * @return void
     * @throws DBALException
     */
    public function setUp(): void
    {
        $schema = $this->connection->getSchemaManager();
        list($tables, $alters) = $this->createSchema(get_class($this));

        foreach ($tables as $tableName => $table) {
            if (!$schema->tablesExist([$tableName])) {
                $schema->createTable($table);
            }
        }

        foreach ($alters as $alter) {
            $schema->alterTable($alter);
        }
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
     * Returns data select.
     *
     * @return DataSelect
     */
    public function select(): DataSelect
    {
        $query = new DataSelect($this);
        $query->select('*');
        $query->from($this::NAME);
        return $query;
    }

    /**
     * Returns select data query result.
     *
     * @param DataSelect $select
     * @return array
     */
    public function fetch(DataSelect $select): array
    {
        $stmt = $select->execute();
        return $stmt->fetchAll();
    }

    /**
     * Insert data.
     *
     * @param array $data
     * @return int Insert ID.
     * @throws DBALException
     */
    public function insert(array $data): int
    {
        $this->connection->insert($this::NAME, $data);
        return $this->connection->lastInsertId();
    }

    /**
     * Update data.
     *
     * @param array $data
     * @return int Affected rows.
     * @throws DBALException
     */
    public function update(array $data): int
    {
        return $this->connection->update($this::NAME, $data, ['id' => $data['id']]);
    }
}
