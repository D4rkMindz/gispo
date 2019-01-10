<?php
ini_set('display_errors', false);
error_reporting(-1);

use Aura\Session\Session;
use Symfony\Component\Translation\Translator;

$config = [];

$applicationName = 'gispo';

$config = [
    'displayErrorDetails' => false,
    'determineRouteBeforeAppMiddleware' => true,
    'addContentLengthHeader' => false,
];

$config[Session::class] = [
    'name' => $applicationName,
    'cache_expire' => 14400,
];

$config[Translator::class] = [
    'locale' => 'de_CH',
    'path' => __DIR__ . '/../resources/locale',
];

$config['migrations'] = __DIR__ . '/../resources/migrations';

$config['db'] = [
    'database' => 'gispo',
    'charset' => 'utf8',
    'encoding' => 'utf8',
    'collation' => 'utf8_unicode_ci',
];

$config['db_test'] = [
    'database' => 'slim_test',
    'charset' => 'utf8',
    'encoding' => 'utf8',
    'collation' => 'utf8_unicode_ci',
];

$config['twig'] = [
    'viewPath' => __DIR__ . '/../templates',
    'cachePath' => __DIR__ . '/../tmp/cache/twig',
    'publicPath' => __DIR__ . '/../public',
    'autoReload' => true,
    'assetCache' => [
        'path' => __DIR__ . '/../public/assets',
        // Cache settings
        'cache_enabled' => true,
        'cache_path' => __DIR__ . '/../tmp/cache',
        'cache_name' => 'assets',
        'cache_lifetime' => 1,
    ],
];

$config['session'] = [
    'name' => 'app_template',
    'autorefresh' => true,
    'lifetime' => '2 hours',
    'path' => '/', //default
    'domain' => null, //default
    'secure' => false, //default
    'httponly' => false, //default
];

$config['logger'] = [
    'main' => 'app',
    'context' => [
    ],
];

if (file_exists(__DIR__ . '/env.php')) {
    $env = require_once __DIR__ . '/env.php';
} elseif (file_exists(__DIR__ . '/../../env.php')) {
    $env = require_once __DIR__ . '/../../env.php';
} else {
    throw new RuntimeException('ENV not found');
}
$config = array_replace_recursive($config, $env);

return $config;
