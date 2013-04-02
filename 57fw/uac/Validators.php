<?php
namespace Uac;

class Validators extends \Core\ValidatorsCase {
    public static function logged($req) {
        if (!$req->user) {
            return new \Http\RedirectResponse(
                self::callClosure(self::$e->uac->config('login_url'))
            );
        }
    }

    public static function notLogged($req) {
        if ($req->user) {
            return new \Http\RedirectResponse(
                self::callClosure(self::$e->uac->config('logged_url'))
            );
        }
    }

    public static function super($req) {
        if (static::logged($req)) 
            return static::logged($req);
        
        if (!$req->user->su)
            return new \Http\RedirectResponse('/');
    }

    public static function callClosure($fn) {
        if (is_callable($fn)) {
            $fn = $fn(self::$e);
        }

        return $fn;
    }
}
