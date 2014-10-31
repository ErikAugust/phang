<?php

// Base include file:
require_once __DIR__.'/../php/base_include.php';

// Use Symfony components:
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;


use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Silex\Application;

// Use app models:
use models\Base;
use controllers\AppController;

// Init Silex:
$app = new Application();

// Session handler - stores session in "../php/session"
$app->register(new Silex\Provider\SessionServiceProvider(), array(
    'session.storage.save_path' => __DIR__.'/../php/session',
));

// Decodes headers with JSON Content-Type automatically
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

// Init Cookie -> Session handling
/*$app->before(function (Request $request) use ($app) {
    if(!$app['session']->has("Example")) {
        $cookies = $request->cookies;
        if($cookies->has("Example")) {
            $app['session']->set("Example", $cookies->get("Example"));
        }
    }
});*/

// Twig HTML templating/rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../php/views',
));


// Route to SPA index twig template:
$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig');
});

// Extended routes - for REST API creation - generated routes are located in /../php/config/routes.yml
$app['routes'] = $app->extend('routes', function (RouteCollection $routes, Application $app) {

    $locator = new FileLocator(array(__DIR__ . "/../php/config/"));
    $loader = new YamlFileLoader($locator);
    $collection = $loader->load('routes.yml');

    $routes->addCollection($collection);

    return $routes;
});


// App debug mode - when set to TRUE app will display errors.
$app['debug'] = true;
$app->run();
