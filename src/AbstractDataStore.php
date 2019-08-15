<?php

namespace Webino;

/**
 * Class AbstractDataStore
 * @package data-store
 */
abstract class AbstractDataStore implements
    DataStoreInterface,
    EventEmitterInterface
{
    use EventEmitterTrait;

    /**
     * @return DataConnection
     */
    abstract public function getConnection(): DataConnection;
}
