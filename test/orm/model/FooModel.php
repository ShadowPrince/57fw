<?php
namespace Test\Orm\Model;

class FooModel extends \Orm\Model {
    public static $table;
    public static $pkey = 'id';

    public $id = 'new \Orm\Field\IntPKey()';
}
