<?php
define(EL_PRE, 1);
define(EL_REQUEST, 2);
define(EL_RESPONSE, 3);
define(START, microtime(1));

spl_autoload_register(function ($classname) {
    $parts = explode('\\', $classname);
    $class = array_pop($parts);
    $fwnamespaces = array(
        'Core',
        'Http',
        'Orm',
        'Routing',
    );
    if (array_search($parts[0], $fwnamespaces) !== false)
        $parts = array_merge(['57fw'], $parts);
    $namespace = strtolower(implode('/', $parts));
    include_once $namespace . DIRECTORY_SEPARATOR . $class . '.php';
});

$e = new \Core\Engine(new Config\Engine());

$e->register('router', (new Routing\Router($e)));
$e->register('http', (new Http\Http($e)));
$e->register('manager', function ($model) {
    global $e;
    if (is_string($model)) {
         
    } else if ($model instanceof \Orm\Model) {
        $model = get_class($model);
    }
    return new \Config\ConnectedManager($e, $model);
});

if (!$cli)
    $e->proceed();

print microtime(1) - START;
