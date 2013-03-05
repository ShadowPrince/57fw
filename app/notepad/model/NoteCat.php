<?php
namespace App\Notepad\Model;

class NoteCat extends \Orm\Model {
    public static $pkey = 'id';
    public static $table = 'notecat';

    public $id = 'new \Orm\Field\IntPKey()';
    public $name = 'new \Orm\Field\Varchar(64)';
}
