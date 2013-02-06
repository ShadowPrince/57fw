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

$e = new Core\Engine(new Config\Engine());

$e->register('router', (new Routing\Router($e)));
$e->register('http', (new Http\Http($e)));

class MM extends Orm\Manager {
    public function init() {
        $this->backend = (new Orm\Backend\MySQL($e));
    }
}

class M extends Orm\Model {
    public $table = 'test';
    public function init() {
        $this->id = new Orm\Field\PrimaryKey();
        $this->name = new Orm\Field\Field();
        $this->text = new Orm\Field\Field();
    }
}


$m = new M();
$mm = new MM(M);
print_r($mm->get(1));

$e->router()->register('#(?P<a>[^,]+),(?P<b>[^,]+)#', function ($e, $b) {
    return $e->http()->getRequestPath();
});

$lvls = array(EL_PRE, EL_REQUEST, EL_RESPONSE);
foreach ($lvls as $lvl)
    echo $e->proceedLevel($lvl);

