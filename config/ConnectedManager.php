<?php
namespace Config;

class ConnectedManager extends \Orm\Manager {
    public $backend = "(new \Orm\Backend\MySQL\MySQL(\$this->e, array(
            'user' => 'root',
            'password' => '1',
            'host' => 'localhost',
            'database' => '57fw',
        ) ))
        ";

}
