<?php
namespace Test\Orm\Model;

class FailModel extends \Orm\Model {
    public static $table = 'test_fail';
    
    public $id = 'new \Orm\Field\IntPKey()';
}
