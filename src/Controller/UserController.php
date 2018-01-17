<?php
/**
 * Created by PhpStorm.
 * User: Björn Pfoster
 * Date: 13.01.2018
 * Time: 11:00
 */

namespace App\Controller;


use App\Model\UserHasActionModel;
use App\Model\UserModel;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class UserController
 */
class UserController extends AppController
{
    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * UserController constructor.
     * @param Container $container
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userModel = $container->get(UserModel::class);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getUserAction(Request $request, Response $response, array $args)
    {
        $userId = (string)$args['user_id'];

        $user = $this->userModel->getUser($userId);
        $found = !empty($user);

        if ($found) {
            $user = $this->formatUser($user);
            $user['last_actions'] = $this->userHasActionModel->getActions($userId);
        }

        $viewData = [
            'found' => $found,
            'user'=> $user,
        ];

        return $this->render($response, $request, 'User/user.index.twig', $viewData);
    }


}
