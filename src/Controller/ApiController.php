<?php

namespace App\Controller;

use App\Model\UserModel;
use App\Service\CSVGenerator;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;

/**
 * Class ApiController
 */
class ApiController extends AppController
{
    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * ApiController constructor.
     *
     * @param Container $container
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userModel = $container->get(UserModel::class);
    }

    /**
     * Get all users action.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getAllUsersAction(Request $request, Response $response): Response
    {
        $users = $this->userModel->getAllUsers();

        foreach ($users as $key => $user) {
            $users[$key] = $this->formatUser($user);
            $users[$key]['email'] = str_replace('@gibmit.ch', '', $user['email']);
        }

        return $this->json($response, ['users' => $users]);
    }

    /**
     * Find user.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function findUserAction(Request $request, Response $response): Response
    {
        $data = $request->getParams();
        $barcode = $data['barcode'] ?: null;
        $firstName = $data['first_name'] ?: null;
        $lastName = $data['last_name'] ?: null;
        $email = $data['email'] ?: null;

        if (empty($barcode) && empty($firstName) && empty($lastName) && empty($email)) {
            return $this->json($response, ['status' => 0, 'message' => __('Please provide any data')]);
        }

        $users = $this->userModel->findUser($barcode, $firstName, $lastName, $email);

        foreach ($users as $key => $user) {
            $users[$key] = $this->formatUser($user);
            $users[$key]['email'] = str_replace('@gibmit.ch', '', $user['email']);
        }

        return $this->json($response, ['users' => $users]);
    }

    /**
     * Check in user action.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function checkInAction(Request $request, Response $response): Response
    {
        $userId = (string)$request->getParsedBodyParam('user_id');
        $barcode = (string)$request->getParsedBodyParam('barcode');
        $barcode = str_replace(' ', '', $barcode);
        if (!empty($barcode)) {
            $userId = $this->userModel->getIdByBarcode($barcode);
        }

        if (empty($userId)) {
            $responseData = [
                'message' => __('User does not exist'),
            ];

            return $this->json($response, $responseData);
        }

        $lastAction = $this->userHasActionModel->getLastAction($userId);
        $name = $this->userModel->getFullName($userId);

        if (!empty($lastAction) && $lastAction['id'] == 1) {
            $responseData = [
                'message' => __('%s already checked in check in', $name),
            ];

            return $this->json($response, $responseData, 422);
        }

        $this->userHasActionModel->checkIn($userId);

        $responseData = [
            'message' => __('%s checked in', $name),
            'button' => __('Check out'),
        ];

        return $this->json($response, $responseData);
    }

    /**
     * Check in user action.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function checkOutAction(Request $request, Response $response): Response
    {
        $userId = (string)$request->getParsedBodyParam('user_id');
        $barcode = (string)$request->getParsedBodyParam('barcode');
        $barcode = str_replace(' ', '', $barcode);
        if (!empty($barcode)) {
            $userId = $this->userModel->getIdByBarcode($barcode);
        }

        if (empty($userId)) {
            $responseData = [
                'message' => __('User does not exist'),
            ];

            return $this->json($response, $responseData);
        }

        $lastAction = $this->userHasActionModel->getLastAction($userId);
        $name = $this->userModel->getFullName($userId);

        if (!empty($lastAction) && $lastAction['id'] == 2) {
            $responseData = [
                'message' => __('%s already checked out', $name),
            ];

            return $this->json($response, $responseData, 422);
        }

        $this->userHasActionModel->checkOut($userId);

        $responseData = [
            'message' => __('%s checked out', $name),
            'button' => __('Check in'),
        ];

        return $this->json($response, $responseData);
    }

    /**
     * Get CSV Action
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getCsvAction(Request $request, Response $response): Response
    {
        $actions = $this->userHasActionModel->getAllActions();

        $csvGenerator = new CSVGenerator();
        $path = $csvGenerator->generate($actions);
        $fh = fopen($path, 'rb');

        $stream = new Stream($fh); // create a stream instance for the response body

        return $response->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', 'attachment; filename="' . basename($path) . '"')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public')
            ->withBody($stream);
    }
}
