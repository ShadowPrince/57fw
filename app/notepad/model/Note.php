<?php
namespace App\Notepad\Model;

class Note extends \Orm\Model {
    public static $pkey = 'id';
    public static $table = 'note';
    public static $order = 'created';

    public $id = 'new \Orm\Field\IntPKey()';
    public $cat = 'new \Orm\Field\ForeignKey("\App\Notepad\Model\NoteCat")';
    public $title = 'new \Orm\Field\Varchar(64)';
    public $text = 'new \Orm\Field\Text()';
    public $created = 'new \Orm\Field\DateTime(array("auto" => 1))';
}
