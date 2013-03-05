<?php
$cli = true;
include 'index.php';

function prepareDatabase($e, $opts, $print_callback, $app=false) {
    foreach ($e->getApps(EL_PRE) as $instance)
        if (!$app || get_class($instance) == $app)
            $instance->prepareDatabase($e, $opts, $print_callback);
}

function appPath($name) {
    return 'app' . DIRECTORY_SEPARATOR . strtolower($name) . DIRECTORY_SEPARATOR;
}

function startapp($name) {
    mkdir(appPath($name) . 'models', 0755, 1);
    $f = fopen(appPath($name) . 'Urls.php', 'w');
    fwrite($f, sprintf(
'<?php
namespace %s\%s;

class Urls implements \Routing\Urls {
    public function init($e) {

    }
}',
        'App',
        $name
    ));
    fclose($f);
}

function parseArguments($argv) {
    array_shift($argv);
    $out = array();
    foreach ($argv as $arg) {
        if (substr($arg, 0, 2) == '--') {
            $eqPos = strpos($arg, '=');
            if ($eqPos === false) {
                $key = substr($arg, 2);
                $out[$key] = isset($out[$key]) ? $out[$key] : true;
            } else {
                $key = substr($arg, 2, $eqPos - 2);
                $out[$key] = substr($arg, $eqPos + 1);
            }
        }
        else if (substr($arg, 0, 1) == '-') {
            if (substr($arg, 2, 1) == '=') {
                $key = substr($arg, 1, 1);
                $out[$key] = substr($arg, 3);
            } else {
                $chars = str_split(substr($arg, 1));
                foreach ($chars as $char) {
                    $key = $char;
                    $out[$key] = isset($out[$key]) ? $out[$key] : true;
                }
            }
        } else {
            $out[] = $arg;
        }
    }
    return $out;
}

$opts = parseArguments($argv);

print ':: 57fw cli' . PHP_EOL;
switch (reset($opts)) {
    case 'syncdb': 
        print ':: Preparing databases...' . PHP_EOL;
        prepareDatabase($e, $opts, function ($s) {
            print '  .. ' . $s . PHP_EOL;
        });
        print ':: Done ';
    break;
    case 'startapp':
        if (!$opts[1]) {
            $opts[1] = readline('  .. Enter name: ');
        }
        startapp($opts[1]);

    break;
}
