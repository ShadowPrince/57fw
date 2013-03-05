<?php
$cli = true;
include 'index.php';

function prepareDatabase($e, $opts, $print_callback, $app=false) {
    foreach ($e->getApps(EL_PRE) as $instance)
        if (!$app || get_class($instance) == $app)
            $instance->prepareDatabase($e, $opts, $print_callback);
}

$opts = array();
for ($i = 1; $i < count($argv); $i++) {
   $opts[$argv[$i]] = 1; 
}

print ':: 57fw cli' . PHP_EOL;
print ':: Preparing databases...' . PHP_EOL;
prepareDatabase($e, $opts, function ($s) {
    print '  .. ' . $s . PHP_EOL;
});
print ':: Done ';
