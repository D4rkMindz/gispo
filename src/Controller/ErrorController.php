<?php

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ErrorController
 */
class ErrorController extends AppController
{
    /**
     * Not Found action
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function notFoundAction(Request $request, Response $response): Response
    {
        return $this->render($response, $request, 'Error/error.twig');
    }
}
