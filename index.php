<?php
define(START, microtime(1));

spl_autoload_register(function ($classname) {
    $parts = explode('\\', $classname);
    $class = array_pop($parts);
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

class ExampleModel extends \Orm\Model {
    public $title = 'new \Orm\Field\Varchar(32)';
    public $text = 'new \Orm\Field\Text';
}

$e = new \Core\Engine();
$e
    ->service('router', (new Routing\Router($e)))
    ->service('http', (new Http\Http($e)))
    ->service('man', function ($model) {
        return \Orm\Manager::manGetter($model, '\Config\ConnectedManager');
    })

    ->register('notepad', new \Core\AppDispatcher('\App\Notepad'))
    ->register('router', new \Routing\RouterDispatcher())

    ->router()->register('/(?P<x>\S+)/(?<y>\S+)/', function ($e, $args) {
        $man = $e->man(new ExampleModel);        

        return 'the show must go on';
    })
;

if (!defined('CLI')) {
    $e->engage();
    print '<br /><small>' . (microtime(1) - START) . '</small>';
}
