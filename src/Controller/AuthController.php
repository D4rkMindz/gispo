<?php

namespace App\Controller;

use App\Model\RegisteredUserModel;
use Interop\Container\Exception\ContainerException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AuthController
 */
class AuthController extends AppController
{
    /**
     * @var RegisteredUserModel
     */
    private $registeredUserModel;

    /**
     * AuthController constructor.
     * @param Container $container
     * @throws ContainerException
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->registeredUserModel = $container->get(RegisteredUserModel::class);
    }

    /**
     * Index action
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function indexAction(Request $request, Response $response): Response
    {
        return $this->render($response, $request, 'Auth/auth.index.twig', []);
    }

    /**
     * Authenticate action
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function authenticateAction(Request $request, Response $response): Response
    {
        $json = $request->getBody()->__toString();
        $data = json_decode($json, true);
        $username = (string)(array_key_exists('username', $data) ? $data['username'] : '');
        $password = (string)(array_key_exists('password', $data) ? $data['password'] : '');

        $hash = $this->registeredUserModel->getPasswordForUser($username);
        if (password_verify($password, $hash)) {
            $this->set('logged_in', true);
            return $this->json($response, ['success' => true]);
        }

        return $this->json($response, ['success' => false]);
    }

    /**
     * Deauthenticate user.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function deauthenticateAction(Request $request, Response $response)
    {
        $this->clear();
        return $this->redirect($response, '/auth');
    }
}
