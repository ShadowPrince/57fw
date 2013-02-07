<?php
namespace Config;

class Engine {
    public static function apps() {
        $apps = [];
        $el_pre = &$apps[EL_PRE];
        $el_request = &$apps[EL_REQUEST];
        $el_response = &$apps[EL_RESPONSE];
        

        $el_pre[] = new \Core\AppDispatcher('\App\Test');
        $el_response[] = new \Routing\RouterDispatcher();


        return $apps;
    }
}
