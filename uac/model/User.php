<?php
namespace Uac\Model;

class User extends \Orm\Model {
    public static $manager = '\Uac\Manager\UserManager';
    public static $pkey = 'id';
    public static $table = 'user';

    public $id = 'new \Orm\Field\IntPKey()';
    public $su = 'new \Orm\Field\Boolean(array("value" => false))';
    public $username = 'new \Orm\Field\Varchar(32, array("uniq"=>1))';
    public $password = 'new \Orm\Field\Varchar(32)';
    public $email = 'new \Orm\Field\Varchar(128, array("uniq"=>1))';

    public $auth_token = 'new \Orm\Field\Varchar(32, array("null" => 1))';
    public $auth_token_expire = 'new \Orm\Field\DateTime(array("null" => 1))';

    public $profile = 'new \Orm\Field\ForeignKey(null, array("null" => 1))';

    public function populate($e) {
        $this->getField('profile')->setModel($e->uac->config('profile_model'));
    }
}
