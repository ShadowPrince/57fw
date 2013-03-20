<?php
namespace Test\Orm\Model;

class PlainModel extends \Orm\Model {
    public static $table = 'test_plain';
    public static $manager = '\Test\Orm\Model\PlainMan';

    public $text = 'new \Orm\Field\Text(array("value" => "1"))';
}
