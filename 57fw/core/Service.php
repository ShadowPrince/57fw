<?php
namespace Core;

abstract class Service {
//    protected $e;
    protected $config;

    public function __construct($config=array()) {
        $this->config = $config;
    }
    
/*    public function setEngine($e) {
        $this->e = $e;

        return $this;
    }
*/

    /**
     * @param mixed
     * @return mixed
     */
    public function getConfig($k) {
        return $this->config[$k];
    }
}
