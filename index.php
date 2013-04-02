<?php
error_reporting(E_ALL);
define('START', microtime(1));
include '57fw/bootstrap.php';

$e = new \Core\Engine(array(
    'debug' => 1
));
$e
    // http workflow
    ->register('http', (new Http\Http()))
    // required by controller validators
    ->register('validators', new \Core\ValidatorsCase())
    // router
    ->register('router', (new Routing\Router(array(
        'add_trailing_slash' => 1,
        'root' => '',
        'prefix' => '/index.php',
    ))))
    // database
    ->register('db', new \Orm\Backend\PDO\PDO(array(
        'uri' => 'mysql:host=localhost;dbname=57fw',
        'user' => 'root',
        'password' => '1',
        'debug' => $e->config('debug')
    )))
    // orm manager getter
    ->register('man', '\Orm\Manager::manGetter')
    // admin site
    ->register('admin', new \Core\ComponentDispatcher('\Admin\Admin'))
    // user accounts
    ->register('uac', new \Core\ComponentDispatcher('\Uac\Uac', array(
        'secret_token' => '1',
        'url_prefix' => '/',
        'profile_model' => '\Uac\Model\Profile'
    )))
    // twig template engine
    ->register('twig', (new \Twig\Twig(array(
        'path' => 'tpl',
        'cache' => 'tpl/cache',
        'debug' => $e->config('debug')
    ))))
    // twig string engine for forms
    ->register('twig_string', (new \Twig\Twig(array(
        'loader' => 'string'
    ))))
    // router dispatcher (response stored)
    ->register('routerd', new \Routing\RouterDispatcher())
    // router engage (response sended)
    ->register('routerengaged', new \Routing\RouterEngageDispatcher())
;

$e->router->register('/', function ($req) use ($e) {
    return $e->twig->render('mainpage.html');
});

// register apps for admin site
$e->admin->register($e->uac);
$e->admin->register($e->admin);

if (!defined('CLI')) {
    print $e->engage();
}
