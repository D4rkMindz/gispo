<?php

namespace App\Test\Controller;

use App\Controller\AppController;
use App\Test\BaseTest;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Class AppControllerTest
 *
 * @coversDefaultClass App\Controller\AppController
 */
class AppControllerTest extends BaseTest
{
    /**
     * @var AppController
     */
    private $appController;

    /**
     * @var Container
     */
    private $container;

    /**
     * Set up before test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->container = app()->getContainer();
        $this->appController = new AppController($this->container);
    }

    /**
     * Test AppController instance.
     *
     * @covers ::__constructor
     * @return void
     */
    public function testInstance()
    {
        $this->assertInstanceOf(AppController::class, $this->appController);
    }

    /**
     * Test render method
     *
     * @covers ::render
     * @param Request $request
     * @param Response $response
     * @return void
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function testRender(Request $request, Response $response)
    {
        $response = $this->appController->render($response, $request, 'Mail/mail.indexAction.twig');
        $this->assertInstanceOf(Response::class, $response);

        $content = (string)$response->getBody();

        $twig = $this->container->get(Twig::class);
        $responseNew = $twig->render(new Response(), 'Mail/mail.indexAction.twig');
        $expected = (string)$responseNew->getBody();

        $this->assertSame($expected, $content);
    }

    /**
     * Test JSON response.
     *
     * @covers ::json
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testJsonResponse()
    {
        $data = [
            'test' => 'value',
            'value' => [
                'second' => 'val',
                'third' => 'val',
            ],
        ];

        $responseExpected = $this->container->get('response');
        $responseExpected = $responseExpected->withJson($data, 200);
        $response = $this->appController->json($data, 200);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame((string)$responseExpected->getBody(), (string)$response->getBody());
        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * Test redirect.
     *
     * @covers ::redirect
     * @return void
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function testRedirect()
    {
        $expected = $this->container->get('response');
        $expected = $expected->withRedirect('/', 301);
        $response = $this->appController->redirect('/', 301);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame((string)$expected->getBody(), (string)$response->getBody());
        $this->assertSame(301, $response->getStatusCode());
    }
}
