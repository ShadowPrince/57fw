<?php
namespace Test\Orm\Model;

class FooModel extends \Orm\Model {
    public static $pkey = 'id';
    public static $table = 'test_foo';

    public $id = 'new \Orm\Field\IntPKey()';
}
