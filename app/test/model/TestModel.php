<?php
namespace App\Test\Model;

class TestModel extends \Orm\Model {
    public function init() {
        $this->id = new \Orm\Field\IntPKey();

    }
}
