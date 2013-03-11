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
    $namespace = strtolower(implode('/', $parts));

    include_once $namespace . DIRECTORY_SEPARATOR . $class . '.php';
});


$e = new \Core\Engine();
$e
    ->service('router', (new Routing\Router(array(
        'add_trailing_slash' => 1  
    ))))
    ->service('http', (new Http\Http()))
    ->service('db', new \Orm\Backend\MySQL\MySQL(array(
            'user' => 'root',
            'password' => '1',
            'host' => 'localhost',
            'database' => '57fw',
        )))
    ->service('man', function ($model) { 
        global $e;
        return \Orm\Manager::manGetter($e, $model);
    })

    ->register('uac', new \Core\ComponentDispatcher('\Uac\Uac', array(
        'secret_token' => '1'
    )))
    ->register('router', new \Routing\RouterDispatcher())

;

if (!defined('CLI')) {
    print $e->engage();
    print '<br /><small>' . (microtime(1) - START) . '</small>';
}
