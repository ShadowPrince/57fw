<?php
namespace Config;

class ConnectedManager extends \Orm\Manager {
    public function init() {
        $this->backend = (new \Orm\Backend\MySQL($e, [
            'user' => 'root',
            'password' => '1',
            'host' => 'localhost',
            'database' => '57fw',
        ]));
    }
}

