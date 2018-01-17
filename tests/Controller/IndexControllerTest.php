<?php

namespace App\Test\Controller;

use App\Controller\IndexController;
use App\Test\Database\DbUnitBaseTest;
use Slim\Container;
use Slim\Http\Response;
use Slim\Views\Twig;
use PHPUnit\DbUnit\DataSet\ArrayDataSet;

/**
 * Class IndexControllerTest
 *
 * @coversDefaultClass App\Controller\IndexController
 */
class IndexControllerTest extends DbUnitBaseTest
{
    /**
     * @var IndexController
     */
    private $indexController;

    /**
     * @var Container
     */
    private $container;

    private $data = [
        'users' => [
            [
                'id' => 1,
                'username' => 'UserOne',
                'first_name' => 'User',
                'last_name' => 'One',
            ],
            [
                'id' => 2,
                'username' => 'UserTwo',
                'first_name' => 'User',
                'last_name' => 'Two',
            ],
            [
                'id' => 3,
                'username' => 'UserThree',
                'first_name' => 'User',
                'last_name' => 'Three',
            ],
            [
                'id' => 4,
                'username' => 'UserFour',
                'first_name' => 'User',
                'last_name' => 'Four',
            ],
        ]
    ];

    /**
     * Set up before test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->container = app()->getContainer();
        $this->indexController = new IndexController($this->container);
    }

    /**
     * Get data set.
     *
     * @return ArrayDataSet|\PHPUnit\DbUnit\DataSet\IDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet($this->data);
    }

    /**
     * Test instance
     *
     * @return void
     */
    public function testInstance()
    {
        $this->assertInstanceOf(IndexController::class, $this->indexController);
    }

    /**
     * Test indexAction method.
     *
     * @coversNothing
     * @return void
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function testIndex()
    {
        //TODO write Test with mocked database
    }
}
