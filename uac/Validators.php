<?php
namespace Uac;

class Validators extends \Core\ValidatorsCase {
    public static function logged($req) {
        if (!$req->user)
            return (new \Http\Response())->redirect(
                self::$e->uac->config('login_url') 
            );
    }

    public static function notLogged($req) {
        if ($req->user)
            return (new \Http\Response())->redirect(
                self::$e->uac->config('logged_url') 
            );
    }
}
