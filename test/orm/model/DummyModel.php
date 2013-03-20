<?php
namespace Test\Orm\Model;

class DummyModel extends \Orm\Model {
    public static $pkey = 'id';
    public static $table = 'test_dummy';

    public $id = 'new \Orm\Field\IntPKey()';

    public $text = 'new \Orm\Field\Text()';
    public $int = 'new \Orm\Field\Int()';
    public $varchar = 'new \Orm\Field\Varchar(32)';

    public $dt = 'new \Orm\Field\DateTime()';

    public $fkey = 'new \Orm\Field\ForeignKey("\Test\Orm\Model\FooModel")';
    public $fkeyn = 'new \Orm\Field\ForeignKey("\Test\Orm\Model\FooModel", array("null" => 1))';
    public $fklist = 'new \Orm\Field\ForeignList("\Test\Orm\Model\FooModel")';
    public $fklist2 = 'new \Orm\Field\ForeignList("\Test\Orm\Model\FooModel")';
}
