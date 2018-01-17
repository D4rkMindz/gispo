<?php

namespace App\Controller;

use App\Model\UserModel;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class IndexController
 */
class IndexController extends AppController
{

    /**
     * @var UserModel
     */
    private $userMapper;

    /**
     * IndexController constructor.
     *
     * @param Container $container
     * @throws \Interop\Container\Exception\ContainerException
*/
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userMapper = $container->get(UserModel::class);
    }

    /**
     * Index method.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexAction(Request $request, Response $response): Response
    {
        $viewData = [
            'page' => 'Home',
        ];

        return $this->render($response, $request, 'Home/home.index.twig', $viewData);
    }
}
