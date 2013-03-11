<?php
namespace Uac\Model;

class User extends \Orm\Model {
    public static $manager = '\Uac\Manager\UserManager';
    public static $pkey = 'id';
    public static $table = 'user';

    public $id = 'new \Orm\Field\IntPKey()';
    public $username = 'new \Orm\Field\Varchar(32)';
    public $password = 'new \Orm\Field\Varchar(32)';
    public $email = 'new \Orm\Field\Varchar(128)';

    public $auth_token = 'new \Orm\Field\Varchar(32)';
    public $auth_token_expire = 'new \Orm\Field\DateTime()';
}
