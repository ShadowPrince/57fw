<?php
define(EL_PRE, 1);
define(EL_REQUEST, 2);
define(EL_RESPONSE, 3);

spl_autoload_register(function ($classname) {
    $parts = explode('\\', $classname);
    $class = array_pop($parts);
    $namespace = strtolower(implode('/', $parts));
    include_once $namespace . DIRECTORY_SEPARATOR . $class . '.php';
});

$e = new \Core\Engine(new Config\Engine());
$e->register('router', (new Routing\Router($e)));
$e->register('http', (new Http\Http($e)));
$e->register('manager', function ($model) {
    return new \Config\ConnectedManager($model);
});

$e->proceed();
