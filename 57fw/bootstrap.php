<?php

function var_str($var) {
    try {
        $str = '"' . $var . '"';
    } catch (\ErrorException $ex) {
        $str = '"NO_STR"';
    }

    $str .= ' ' . gettype($var);

    if ($var === null) {
        $str = 'NULL';
    }

    if (is_string($var)) {
        $str .= '(' . strlen($var) . ')';
    }
    if (is_array($var)) {
        $str .= '(' . count($var) . ')';
    } 
    if (is_object($var)) {
        $str .= '(' . serialize($var) . ')';
    }

    return $str;
}

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
        'Twig',
        'Form',
        'Admin'
    );

    if (array_search($parts[0], $fw_namespaces) !== false)
        $parts = array_merge(['57fw'], $parts);

    $namespace = strtolower(implode(DIRECTORY_SEPARATOR, $parts));

    $path = __DIR__ . '/../' . $namespace . DIRECTORY_SEPARATOR . $class . '.php';

    if (is_file($path))
        include $path;
});

spl_autoload_register(function ($classname) {
    if (0 !== strpos($classname, 'Twig_'))
        return;

    $path = ''
        . dirname(__FILE__)
        . '/../57fw/twig/'
        . str_replace(array('_', "\0"), array('/', ''), $classname)
        . '.php';

    if (is_file($path)) 
        include $path;
});
