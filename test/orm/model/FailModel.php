<?php
namespace Test\Orm\Model;

class FailModel extends \Orm\Model {
    public static $table;
    
    public $id = 'new \Orm\Field\IntPKey()';
}
