<?php
$app = app();

$app->get('/', route(['App\Controller\IndexController', 'indexAction']))
    ->setName('root');

$app->get('/api/users', route(['App\Controller\ApiController', 'getAllUsersAction']))
    ->setName('get.api.all-users');

$app->post('/api/users/search', route(['App\Controller\ApiController', 'findUserAction']))
    ->setName('post.api.search-user');

$app->post('/api/users/checkin', route(['App\Controller\ApiController', 'checkInAction']))
    ->setName('post.api.checkin-user');

$app->post('/api/users/checkout', route(['App\Controller\ApiController', 'checkOutAction']))
    ->setName('post.api.checkout-user');

$app->get('/api/users/csv', route(['App\Controller\ApiController', 'getCsvAction']))
    ->setName('post.api.checkout-user');

$app->get('/users/{user_id}', route(['App\Controller\UserController', 'getUserAction']))
    ->setName('get.user');
