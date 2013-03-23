<?php
namespace Uac\Model;

class Profile extends \Orm\Model {
    public static $pkey = 'id'; 
    public static $table = 'profile';

    public $id = "new \Orm\Field\IntPKey()";
    public $bio = "new \Orm\Field\Text()";
}
