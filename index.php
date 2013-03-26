<?php
error_reporting(E_ALL);
define('START', microtime(1));
include '57fw/bootstrap.php';

$e = new \Core\Engine(array(
    'debug' => 1
));
$e
    ->register('validators', new \Core\ValidatorsCase())
    ->register('http', (new Http\Http()))
    ->register('router', (new Routing\Router(array(
        'add_trailing_slash' => 1
    ))))
    ->register('db', new \Orm\Backend\PDO\PDO(array(
        'uri' => 'mysql:host=localhost;dbname=57fw',
        'user' => 'root',
        'password' => '1',
        'debug' => $e->config('debug')
    )))
    ->register('man', '\Orm\Manager::manGetter')
    ->register('uac', new \Core\ComponentDispatcher('\Uac\Uac', array(
        'secret_token' => '1',
        'url_prefix' => '/',
        'profile_model' => '\Uac\Model\Profile'
    )))
    ->register('twig', (new \Twig\Twig(array(
        'path' => 'tpl',
        'cache' => 'tpl/cache',
        'debug' => $e->config('debug')
    ))))
    ->register('rd', new \Routing\RouterDispatcher())
    ->register('red', new \Routing\RouterEngageDispatcher())
;

$e->router->register('/', function ($req) use ($e) {
    return $e->twig->render('mainpage.html');
});
$e->router->register('/test/(?P<x>\w+)/(\w+)/', function ($req) use ($e) {
        return $req->user;
    })->validate('\Uac\Validators::logged')->bind('1');

if (!defined('CLI')) {
    print $e->engage();

    print '<br /><small>time: ' . (microtime(1) - START) . '</small>';
}
