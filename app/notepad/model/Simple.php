<?php
namespace App\Notepad\Model;

class Simple extends \Orm\Model {
    public static $table = 'simple';
    public static $pkey = 'id';

    public $dt = 'new \Orm\Field\Int()';
    public $id = 'new \Orm\Field\IntPKey()';
    public $text = 'new \Orm\Field\Text()';
    public $x = 'new \Orm\Field\ForeignList("\App\Notepad\Model\Note")';
}
