<?php
define(START, microtime(1));

error_reporting(E_ALL);

spl_autoload_register(function ($classname) {
    $parts = explode('\\', $classname);
    $class = array_pop($parts);
    if (!$parts)
        $parts[] = '\\';
    $fw_namespaces = array(
        'Core',
        'Http',
        'Orm',
        'Routing',
    );
    if (array_search($parts[0], $fw_namespaces) !== false)
        $parts = array_merge(['57fw'], $parts);
    $namespace = strtolower(implode(DIRECTORY_SEPARATOR, $parts));

    include_once $namespace . DIRECTORY_SEPARATOR . $class . '.php';
});


$e = new \Core\Engine();
$e

    ->register('http', (new Http\Http()))

    // modifying request is now available
    ->register('router', (new Routing\Router(array(
        'add_trailing_slash' => 1
    ))))
    ->register('db', new \Orm\Backend\MySQL\MySQL(array(
            'user' => 'root',
            'password' => '1',
            'host' => 'localhost',
            'database' => '57fw',
            'debug' => 1
        )))
    ->register('man', function ($model) { 
        global $e;
        return \Orm\Manager::manGetter($e, $model);
    })

    ->register('uac', new \Core\ComponentDispatcher('\Uac\Uac', array(
        'secret_token' => '1',
        'url_prefix' => '/uac/'
    )))
    
    // modify request is now unavailable
    ->register('router_dispatcher', new \Routing\RouterDispatcher())

    // modifying response is now unavailable
    ->register('router_dispatcher_response', new \Routing\RouterDispatcher(array(
        'engage_response' => 1
    )))

    ->router()->register(null, '/', function ($req) { 
        global $e;
        $res = '';
        if ($req->user) {
            $res .= 'Logged as ' . $req->user->username . '<br>';
            $res .= 'Su: ' . $req->user->su . ', email: ' . $req->user->email . '<br>';
        }
        $res .= 'mainpage';
        return $res;
    })

;

if (!defined('CLI')) {
    print $e->engage();
    print '<br /><small>' . (microtime(1) - START) . '</small>';
}
