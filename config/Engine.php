<?php
namespace Config;

class Engine {
    public $baseUrl = '/57fw/index.php';
    public $apps = array(
        EL_RESPONSE => array('Routing\RouterDispatcher'),
    );
}
