<?php

namespace Webino;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class DataSelect
 * @package data-store
 */
class DataSelect extends QueryBuilder
{
    /**
     * @var AbstractDataStore
     */
    protected $store;

    /**
     * @param AbstractDataStore $store
     */
    public function __construct(AbstractDataStore $store)
    {
        parent::__construct($store->getConnection());
        $this->store = $store;
    }

    public function select($select = null)
    {
        $this->store->emit('data_select_event');

        return parent::select($select);
    }
}
