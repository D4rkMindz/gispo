<?php
$app = app();

$app->get('/', 'App\Controller\IndexController:indexAction')
    ->setName('root');

$app->get('/api/users', 'App\Controller\ApiController:getAllUsersAction')
    ->setName('get.api.all-users');

$app->post('/api/users/search', 'App\Controller\ApiController:findUserAction')
    ->setName('post.api.search-user');

$app->post('/api/users/checkin', 'App\Controller\ApiController:checkInAction')
    ->setName('post.api.checkin-user');

$app->post('/api/users/checkout', 'App\Controller\ApiController:checkOutAction')
    ->setName('post.api.checkout-user');

$app->get('/api/users/csv', 'App\Controller\ApiController:getCsvAction')
    ->setName('post.api.checkout-user');

$app->get('/users/{user_id}', 'App\Controller\UserController:getUserAction')
    ->setName('get.user');

$app->get('/auth', 'App\Controller\AuthController:indexAction')
    ->setName('get.auth');

$app->post('/auth', 'App\Controller\AuthController:authenticateAction')
    ->setName('post.auth');

$app->get('/deauth', 'App\Controller\AuthController:deauthenticateAction')
    ->setName('get.deauth');
