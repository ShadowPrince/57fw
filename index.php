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

echo '<pre>';
$e = new Core\Engine(new Config\Engine());

class MM extends Orm\Manager {
    public function init() {
        $this->backend = (new Orm\Backend\MySQL($e, [
            'user' => 'root',
            'password' => '1',
            'host' => 'localhost',
            'database' => '57fw',
        ]));
    }
}

$e->register('router', (new Routing\Router($e)));
$e->register('http', (new Http\Http($e)));
$e->register('manager', function ($model) {
    return new MM($model);
});

$e->router()->register('#(?P<a>[^,]+),(?P<b>[^,]+)#', function ($e, $b) {
    return $e->http()->getRequestPath();
});

$lvls = array(EL_PRE, EL_REQUEST, EL_RESPONSE);
foreach ($lvls as $lvl)
    echo $e->proceedLevel($lvl);

