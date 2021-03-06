<?php
namespace Test\Orm\Model;

class BarModel extends \Orm\Model {
    public static $table;
    public static $pkey = 'id';

    public $id = 'new \Orm\Field\IntPKey()';
    public $foo = 'new \Orm\Field\Varchar(32)';
    public $bar = 'new \Orm\Field\DateTime()';
    public $two_thousands = 'new \Orm\Field\ForeignKey("\Test\Orm\Model\BarModel")';
}
