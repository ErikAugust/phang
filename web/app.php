<?php
require_once __DIR__.'/../php/base_include.php';

// Init Silex:
$app = new Silex\Application();

// Use Symfony components:
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;

// Use app models:
use models\Base;

// Session handler - stores session in "../../session"
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
$app->before(function (Request $request) use ($app) {
	if(!$app['session']->has("Example")) {
		$cookies = $request->cookies;
    	if($cookies->has("Example")) {
        	$app['session']->set("Example", $cookies->get("Example"));
		}
	}
});

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
    return $app->redirect('/index.html');
});

// REST API routing:
$app->get('/user/', function () use ($app) {
    return $app->json('Hello world!');
});

// App debug mode - when set to TRUE app will display errors.
$app['debug'] = true;
$app->run();
