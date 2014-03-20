<?php
require_once __DIR__.'/../php/base_include.php';

// Use Symfony components:
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Silex\Application;

// Use app models:
use models\Base;
use models\AppController;

// Init Silex:
$app = new Application();

// Session handler - stores session in "../../session"
/*$app->register(new Silex\Provider\SessionServiceProvider(), array(
    'session.storage.save_path' => __DIR__.'/../php/session',
));*/

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

// Silex Memcache Provider:
$app->register(new SilexMemcache\MemcacheExtension(), array(
    'memcache.library'    => MEMCACHE_LIBRARY,
    'memcache.server' => array(
        array(MEMCACHE_HOST, MEMCACHE_PORT)
    )
));

// Routing to SPA (Single Page Application)
// PHANG, by default uses the single page application format:
$app->get('/', function () use ($app) {
	$base = new Base;
	return $app->redirect('/index.html');
});


// Extended routes
$app['routes'] = $app->extend('routes', function (RouteCollection $routes, Application $app) {
    $loader     = new YamlFileLoader(new FileLocator(__DIR__ . '/../php/config'));
    $collection = $loader->load('routes.yml');
    $routes->addCollection($collection);
 
    return $routes;
});


// App debug mode - when set to TRUE app will display errors.
$app['debug'] = true;
$app->run();
