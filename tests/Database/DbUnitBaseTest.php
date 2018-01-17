<?php

namespace App\Test\Database;


use App\Test\BaseTest;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use PDO;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;
use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\Operation\Factory;
use PHPUnit\DbUnit\TestCaseTrait;
use Exception;
use Slim\Container;

abstract class DbUnitBaseTest extends BaseTest
{
    use TestCaseTrait;

    /**
     * @var PDO
     */
    private static $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    /**
     * @var Container
     */
    private $container;

    /**
     * Get Connection.
     *
     * @return DefaultConnection
     */
    public function getConnection(): DefaultConnection
    {
        if ($this->conn === null) {
            $this->conn = $this->createDefaultDBConnection(static::getPdo());
        }

        return $this->conn;
    }

    /**
     * Get PDO object.
     *
     * @return PDO
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected static function getPdo(): PDO
    {
        if (!self::$pdo) {
            app()->getContainer()[Connection::class] = function (Container $container) {
                $config = $container->get('settings')->get('db_test');
                $driver = new Mysql([
                    'host' => $config['host'],
                    'port' => $config['port'],
                    'database' => $config['database'],
                    'username' => $config['username'],
                    'password' => $config['password'],
                    'encoding' => $config['encoding'],
                    'charset' => $config['charset'],
                    'collation' => $config['collation'],
                    'prefix' => '',
                    'flags' => [
                        // Enable exceptions
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        // Set default fetch mode
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_PERSISTENT => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8 COLLATE utf8_unicode_ci",
                    ],
                ]);
                $db = new Connection([
                    'driver' => $driver,
                ]);
                $db->connect();

                return $db;
            };

            self::$pdo = app()->getContainer()->get(Connection::class)->getDriver()->connection();
        }

        return self::$pdo;
    }

    /**
     * Setup before Class.
     *
     * This code will be executed before every class. This makes sure, that the test-database is always in the same
     * "test-state".
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function setUpBeforeClass()
    {
        static::getPdo();
    }

    /**
     * Setup.
     *
     * This code will be executed once before the tests are executed.
     *
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setUp()
    {
        $this->container = app()->getContainer();

        static $setup = false;
        if (!$setup) {
            $this->setupDatabase();
            $setup = true;
        }
        $this->truncateTables();
        Factory::INSERT()->execute($this->getConnection(), $this->getDataSet());
    }

    /**
     * Truncate all Tables.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function truncateTables()
    {
        $pdo = static::getPdo();
        $stmt = $pdo->query('SHOW TABLES');
        while ($row = $stmt->fetch()) {
            $table = array_values($row)[0];
            $pdo->prepare(sprintf('TRUNCATE TABLE `%s`', $table))->execute();
        }
    }

    /**
     * Setup Database.
     *
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setupDatabase()
    {
        $pdo = static::getPdo();
        $stmt = $pdo->query('SHOW TABLES');
        while ($row = $stmt->fetch()) {
            $table = array_values($row)[0];
            $pdo->prepare(sprintf('DROP TABLE `%s`', $table))->execute();
        }
        chdir(__DIR__ . '/../config');
        $wrap = new TextWrapper(new PhinxApplication());
        // Execute the command and determine if it was successful.
        $env = 'local';
        $target = null;
        call_user_func([$wrap, 'getMigrate'], $env, $target);
        $error = $wrap->getExitCode() > 0;
        if ($error) {
            throw new Exception('Error: Setup database failed with exit code: %s', $wrap->getExitCode());
        }
    }

    /**
     * Generate Update row.
     *
     * @param array $row
     * @return array
     */
    public function generateUpdateRow(array $row): array
    {
        foreach ($row as $key => $value) {
            if (preg_match('/\w*(id)\w*/', $key)) {
                continue;
            }
            $converted = preg_replace('/[ÄÖÜäöüÉÈÀéèà]/', "", $row[$key]);
            $parts = str_split(html_entity_decode($converted));
            sort($parts);
            $row[$key] = implode($parts);
        }

        return $row;
    }
}

