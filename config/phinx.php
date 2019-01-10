<?php

use Cake\Database\Connection;

require_once __DIR__ . '/bootstrap.php';
$container = app()->getContainer();
$pdo = $container->get(Connection::class);
$pdo = $pdo->getDriver()->getConnection();

return [
    'paths' => [
        'migrations' => $container->get('settings')->get('migrations'),
    ],
    'environments' => [
        'default_database' => 'local',
        'local' => [
            'name' => $container->get('settings')->get('db')['database'],
            'connection' => $pdo,
        ],
    ],
];

