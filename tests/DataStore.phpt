<?php
/**
 * Webino™ (http://webino.sk)
 *
 * @noinspection PhpUnhandledExceptionInspection
 * @link        https://github.com/webino/data-store
 * @copyright   Copyright (c) 2019 Webino, s.r.o. (http://webino.sk)
 * @author      Peter Bačinský <peter@bacinsky.sk>
 * @license     BSD-3-Clause
 */

namespace Webino;

use Tester\Assert;
use Tester\Environment;

Environment::setup();


class ExampleDataStore extends AbstractTableDataStore
{
    public const NAME = 'example';

    public const COLUMNS = [
        'label' => [
            'type' => 'string',
            'length' => 100,
        ],
        'text' => [
            'type' => 'text',
            'length' => 2000,
            'notnull' => false,
        ],
        'subs' => ExampleSubDataStore::class,
    ];
}

class ExampleSubDataStore extends AbstractTableDataStore
{
    public const NAME = 'example_sub';

    public const COLUMNS = [
        'name' => [
            'type' => 'string',
            'length' => 100,
        ],
        'description' => [
            'type' => 'text',
            'length' => 2000,
            'notnull' => false,
        ],
    ];
}

class ExampleTwoDataStore extends AbstractTableDataStore
{
    public const NAME = 'example_two';

    public const COLUMNS = [
        'number' => [
            'type' => 'string',
            'length' => 100,
        ],
        'note' => [
            'type' => 'text',
            'length' => 2000,
            'notnull' => false,
        ],
    ];
}


$container = new InstanceContainer;

$options = new MySqlDataConnectionOptions;
$options->host = '127.0.0.1';
$options->user = 'jsrjne';
$options->password = 'z9fn1n6dumas';
$options->database = 'mydb';

/** @var DataConnection $db */
$db = $container->make(DataConnection::class, $options);

$db->setUp();

/** @var ExampleDataStore $exampleStore */
$exampleStore = $container->make(ExampleDataStore::class, $db);
/** @var ExampleTwoDataStore $exampleTwoStore */
$exampleTwoStore = $container->make(ExampleTwoDataStore::class, $db);

$exampleStore->setUp();
$exampleTwoStore->setUp();

$isConnected = $db->isConnected();

$id = $exampleStore->insert(['label' => 'pokus', 'text' => 'lorem ipsum test bla bla...']);
$exampleStore->update(['id' => $id, 'label' => 'pokus updated']);

$exampleStore->insert(['label' => 'pokus2']);

// TODO !!!

$exampleStore->on('data_select_event', function () {
    die('Event OK');
});

$exampleSelect = $exampleStore->select();

$exampleSelect->select(['subs']);

die($exampleSelect->getSQL());

$exampleData = $exampleStore->fetch($exampleSelect);

//dd($data);


$exampleTwoStore->insert(['number' => '123456', 'note' => 'special note test bla bla...']);
$exampleTwoStore->insert(['number' => '654789']);

$exampleTwoSelect = $exampleTwoStore->select();
$exampleTwoData = $exampleStore->fetch($exampleTwoSelect);

//$db->tearDown();
$db->close();
$isNotConnected = $db->isConnected();


Assert::true($isConnected);
Assert::false($isNotConnected);
Assert::same(require 'DataStore.exampleData.php', $exampleData);
Assert::same(require 'DataStore.exampleTwoData.php', $exampleTwoData);
