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
$options->user = 'b6nhbl';
$options->password = 'rlj8e2ad';
$options->database = 'mydb';

/** @var DataConnection $conn */
$conn = $container->make(DataConnection::class, $options);

$conn->createDatabase();

/** @var ExampleDataStore $exampleStore */
$exampleStore = $container->make(ExampleDataStore::class, $conn);
/** @var ExampleTwoDataStore $exampleTwoStore */
$exampleTwoStore = $container->make(ExampleTwoDataStore::class, $conn);

$exampleStore->setUp();
$exampleTwoStore->setUp();

$isConnected = $conn->isConnected();


$exampleStore->insert(['label' => 'pokus', 'text' => 'lorem ipsum test bla bla...']);
$exampleStore->insert(['label' => 'pokus2']);

$exampleTwoStore->insert(['number' => '123456', 'note' => 'special note test bla bla...']);
$exampleTwoStore->insert(['number' => '654789']);

$conn->close();


Assert::true($isConnected);
