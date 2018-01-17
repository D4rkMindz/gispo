<?php

namespace App\Controller;

use App\Model\UserHasActionModel;
use Aura\Session\Segment;
use Aura\Session\Session;
use Interop\Container\Exception\ContainerException;
use Monolog\Logger;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;

/**
 * Class AppController
 */
class AppController
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Segment
     */
    protected $segment;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @var UserHasActionModel
     */
    protected $userHasActionModel;

    /**
     * AppController constructor.
     *
     * @param Container $container
     * @throws ContainerException
     */
    public function __construct(Container $container)
    {
        $this->request = $container->get('request');
        $this->response = $container->get('response');
        $this->session = $container->get(Session::class);
        $this->segment = $this->session->getSegment('app');
        $this->router = $container->get('router');
        $this->logger = $container->get(Logger::class);
        $this->twig = $container->get(Twig::class);
        $this->userHasActionModel = $container->get(UserHasActionModel::class);
    }

    /**
     * Get value from session.
     *
     * @param $sesionKey
     * @param null $alt
     * @return mixed
     */
    public function get($sesionKey, $alt = null)
    {
        return $this->segment->get($sesionKey, $alt);
    }

    /**
     * Set value into session.
     *
     * @param $sessionKey
     * @param $value
     * @return void
     */
    public function set($sessionKey, $value)
    {
        $this->segment->set($sessionKey, $value);
    }

    /**
     * Render HTML
     *
     * @param Response $response
     * @param Request $request
     * @param string $file
     * @param array $viewData
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function render(Response $response, Request $request, string $file, array $viewData = []): Response
    {
        $extend = [
            'language' => $request->getAttribute('language'),
        ];
        $viewData = array_replace_recursive($extend, $viewData);

        return $this->twig->render($response, $file, $viewData);
    }

    /**
     * Return JSON Response.
     *
     * @param Response $response
     * @param array $data
     * @param int $status
     * @return Response
     */
    public function json(Response $response, $data, int $status = 200): Response
    {
        return $response->withJson($data, $status);
    }

    /**
     * Return redirect.
     *
     * @param Response $response
     * @param string $url
     * @param int $status
     * @return Response
     */
    public function redirect(Response $response, string $url, int $status = 301): Response
    {
        return $response->withRedirect($url, $status);
    }

    /**
     * Format user
     *
     * @param $user
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function formatUser($user)
    {
        $user['img_url'] = !empty($user['photo_file_name']) ? 'img/users/' . $this->map($user['photo_file_name']) : 'img/default.jpg';
        $user['last_action'] = $this->userHasActionModel->getLastAction($user['id']);
        $condition = !empty($user['last_action']) ? $user['last_action']['id'] == 1 : false;
        $user['button'] = $condition ? __('Check out') : __('Check in');
        $user['status'] = $condition ? __('Checked in') : __('Checked out');
        $user['type'] = $condition ? 'success' : 'danger';

        return $user;
    }

    /**
     * Map
     *
     * @param $filename
     * @return string
     */
    private function map($filename)
    {
        $map = [
            "ä" => "ae",
            "ü" => "ue",
            "ö" => "oe",
            "Ä" => "Ae",
            "Ü" => "Ue",
            "Ö" => "Oe",
            "è" => "e",
            "é" => "e",
            "ô" => "o",
            "ë" => "e",
            "'" => "",
        ];

        return strtr($filename, $map);
    }
}
