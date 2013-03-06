<?php
namespace Config;

class Engine {
    public static $apps;
    public function apps() {
        $apps = array();
        $el_pre = &$apps[EL_PRE];
        $el_request = &$apps[EL_REQUEST];
        $el_response = &$apps[EL_RESPONSE];
        /*****/
        /*****/
        return $apps;
    }
}
