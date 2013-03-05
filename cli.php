<?php
$cli = true;
include 'index.php';

function prepareDatabase($e, $app=false) {
    foreach ($e->getApps(EL_PRE) as $instance) {
        if (!$app || get_class($instance) == $app)
            $instance->prepareDatabase($e); 
    }
}

print ':: 57fw cli' . PHP_EOL;
print ':: Preparing databases...' . PHP_EOL;
prepareDatabase($e);
print ':: Done ';
