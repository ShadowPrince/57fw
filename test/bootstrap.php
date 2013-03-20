<?php

spl_autoload_register(function ($classname) {
    if (0 === strpos($classname, 'Twig_')) {
        $path = ''
            . dirname(__FILE__)
            . '/../57fw/twig/'
            . str_replace(array('_', "\0"), array('/', ''), $classname)
            . '.php';

        include $path;
        return;
    }

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
    );

    if (array_search($parts[0], $fw_namespaces) !== false)
        $parts = array_merge(['57fw'], $parts);

    $namespace = strtolower(implode(DIRECTORY_SEPARATOR, $parts));

    $path = '..' . DIRECTORY_SEPARATOR . $namespace . DIRECTORY_SEPARATOR . $class . '.php';
    if (is_file($path))
        include $path;
});
