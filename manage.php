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
    return strtolower($name) . DIRECTORY_SEPARATOR;
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
call_user_func_array(array_shift($opts), array($e, $opts));

/**
 * ACTIONS
 */

function modeldb($e, $opts) {
    if (!isset($opts[0]))
        die('!! modeldb require\'s argument (model)');
    $model = str_replace('/', '\\', array_shift($opts));
    if (!@class_exists($model)) {
        die('Class ' . $model . ' dont exists!');
    }
    print ':: Preparing database for model ' . $model . PHP_EOL;
    $m = $e->man($model);
    $m->prepare($opts, 'dottedPrint');
}

function syncdb($e, $opts) {
    if (isset($opts[0])) 
        $model = $opts[0];
    else
        $model = null;
    print ':: Preparing databases...' . PHP_EOL;
    prepareDatabase($e, $opts, 'dottedPrint', $model);
    print ':: Done ';
}

function runserver($e, $opts) {
    if (isset($opts[0])) {
        $url = array_shift($opts);
    } else {
        $url = '127.0.0.1:8000';
    }
    print ':: Server started at ' . $url;
    system('php -S ' . $url . ' -t .');
}
