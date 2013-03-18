#!/usr/bin/env php
<?php
define('CLI', 1);

include 'index.php';

/**
 * Prepare database
 */
function prepareDatabase($e, $opts, $print_callback, $app=false) {
    foreach ($e->getApps() as $instance) {
        if (!($instance instanceof \Core\ComponentDispatcher))
            continue;
        if (!$app || strtolower($instance->getName()) == strtolower($app))
            $instance->prepareDatabase($e, $opts, $print_callback);
    }
}

/**
 * Get app path
 */
function appPath($name) {
    # return 'app' . DIRECTORY_SEPARATOR .
    return strtolower($name) . DIRECTORY_SEPARATOR;
}

/**
 * Start new app 
 * @param $name
 */
function startapp($name) {
    mkdir(appPath($name) . 'models', 0755, 1);
    $f = fopen(appPath($name) . 'Urls.php', 'w');
    fwrite($f, sprintf(
'<?php
namespace %s\%s;

class Urls implements \Routing\Urls {
    public function engage($e) {

    }
}',
        'App',
        $name
    ));
    fclose($f);
}

/**
 * Parse args
 */
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

function dottedPrint ($s) {
    print '  .. ' . $s . PHP_EOL;
}

$opts = parseArguments($argv);

print ':: 57fw cli' . PHP_EOL;
switch (reset($opts)) {
    case 'modeldb':
        print ':: Preparing database for model ' . $opts[1] . PHP_EOL;
        $m = $e->man($opts[1]);
        $m->prepare($opts, 'dottedPrint');
        
    break;
    case 'syncdb': 
        print ':: Preparing databases...' . PHP_EOL;
        prepareDatabase($e, $opts, 'dottedPrint', isset($opts[1]) ? $opts[1] : false);
        print ':: Done ';
    break;
    case 'startapp':
        if (!isset($opts[1])) {
            $opts[1] = readline('  .. Enter name: ');
        }
        startapp($opts[1]);

    break;

    default:
        print '!! Choose your destiny: (modeldb, syncdb, startapp)';
    break;
}
