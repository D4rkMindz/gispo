<?php

use Aura\Session\Session;
use Slim\Http\Request;
use Slim\Http\Response;

$app = app();
$container = $app->getContainer();

//$app->add(function (Request $request, Response $response, $next) use ($container) {
//    $locale = $request->getAttribute('language');
//    if ($locale == 'de') {
//        $locale = 'de_DE';
//    }
//    $translator = $container->get(Translator::class);
//    $resource = __DIR__ . "/../resources/locale/" . $locale . "_messages.mo";
//    $translator->setLocale($locale);
//    $translator->setFallbackLocales(['en_US']);
//    $translator->addResource('mo', $resource, $locale);
//
//    return $next($request, $response);
//});
//
//$app->add(function (Request $request, Response $response, $next) use ($container) {
//    $language = $request->getAttribute('language');
//    $hasLanguage = !empty($language);
//    if (empty($language)) {
//        // Browser language
//        $language = $request->getHeader('accept-language')[0];
//        $language = explode(',', $language)[0];
//        $language = explode('-', $language)[0];
//    }
//    $whitelist = [
//        'de' => 'de_CH',
//        'en' => 'en_US',
//    ];
//    if (!isset($whitelist[$language])) {
//        throw new NotFoundException($request, $response);
//    }
//    if (!$hasLanguage) {
//        return $response->withRedirect($container->get('router')->pathFor('root', ['language' => $language]));
//    }
//
//    return $next($request, $response);
//});
//
//$app->add(function (Request $request, Response $response, $next) {
//    $route = $request->getAttribute('route');
//    if (empty($route)) {
//        return $next($request, $response);
//    }
//    $language = $route->getArgument('language');
//    $request = $request->withAttribute('language', $language);
//    $response = $next($request, $response);
//
//    return $response;
//});

$app->add(function (Request $request, Response $response, callable $next) use ($container) {
    $relaxed = [
        '/auth' => ['GET' => true, 'POST' => true],
    ];

    /** @var Session $sessionBundle */
    $sessionBundle = $container->get(Session::class);
    $session = $sessionBundle->getSegment('app');

    if ($session->get('logged_in')) {
        return $next($request, $response);
    }

    $route = $request->getRequestTarget();
    $method = strtoupper($request->getMethod());

    if (array_key_exists($route, $relaxed)
        && array_key_exists($method, $relaxed[$route])
        && $relaxed[$route][$method]) {
        return $next($request, $response);
    }

    return $response->withRedirect('/auth', 301);
});

/**
 * Session Middleware
 *
 * @return Response
 */
$app->add(function (Request $request, Response $response, $next) use ($container) {
    /** @var Session $session */
    $session = $container->get(Session::class);
    $session->start();
    $response = $next($request, $response);
    $session->commit();

    return $response;
});
