<?php
define(START, microtime(1));

error_reporting(E_ALL);
spl_autoload_register(function ($classname) {
    if (0 === strpos($classname, 'Twig_')) {
        $path = ''
            . dirname(__FILE__)
            . '/57fw/twig/'
            . str_replace(array('_', "\0"), array('/', ''), $classname)
            . '.php';

        include $path;
        return;
    }

    $parts = explode('\\', $classname);
    $class = array_pop($parts);
    if (!$parts)
        $parts[] = '\\';
    $fw_namespaces = array(
        'Core',
        'Http',
        'Orm',
        'Routing',
        'Twig',
    );
    if (array_search($parts[0], $fw_namespaces) !== false)
        $parts = array_merge(['57fw'], $parts);
    $namespace = strtolower(implode(DIRECTORY_SEPARATOR, $parts));

    include $namespace . DIRECTORY_SEPARATOR . $class . '.php';
});

$e = new \Core\Engine(array(
    'debug' => 1
));
$e
    ->register('http', (new Http\Http()))
    ->register('twig', (new \Twig\Twig(array(
        'path' => 'tpl',
        'cache' => 'tpl/cache',
        'debug' => $e->config('debug')
    ))))
    ->register('router', (new Routing\Router(array(
        'add_trailing_slash' => 1
    ))))
    ->register('db', new \Orm\Backend\PDO\PDO(array(
        'uri' => 'mysql:host=localhost;dbname=57fw',
        'user' => 'root',
        'password' => '1',
        'debug' => $e->config('debug')
    )))
    ->register('man', function ($model) { 
        global $e;
        return \Orm\Manager::manGetter($e, $model);
    })
    ->register('uac', new \Core\ComponentDispatcher('\Uac\Uac', array(
        'secret_token' => '1',
        'url_prefix' => '/uac/'
    )))
    ->register('router_dispatcher', new \Routing\RouterDispatcher())
    ->register('router_dispatcher_response', new \Routing\RouterDispatcher(array(
        'engage_response' => 1
    )))

    ->router->register(null, '/', function ($req) { 
        global $e;
        return $e->twig->render('mainpage.html');
    })
;

if (!defined('CLI')) {
    print $e->engage();
    print '<br /><small>time: ' . (microtime(1) - START) . '</small>';
}
