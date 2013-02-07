<?php
namespace App\Test\Model;

class TestModel extends \Orm\Model {
    public function init() {
        $this->id = new \Orm\Field\IntPKey();  
        $this->login = new \Orm\Field\Varchar(32);
        $this->password = new \Orm\Field\Varchar(32);
    }
}
