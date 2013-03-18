<?php
namespace Uac;

class Uac extends \Core\Component {
    public function engage($e) {
        $this->e = $e;

        $e->router()->register($this, 'login/', array($this, 'login'));
        $e->router()->register($this, 'logout/', array($this, 'logout'));
        $e->router()->register($this, 'register/', array($this, 'register'));

        $this->cookieLogin();
    }

    protected function cookieLogin() {
        $man = $this->e->man('Uac\Model\User');
        $request = $this->e->router()->getRequest();

        if ($request->cookie('uac_token')) {
            $user = $man->authTokenLogin($request->cookie('uac_token'));
            $request->user = $user;
        } else {
            $request->user = null;
        }
    }

    public function register($req) {
        $this->e->man('Uac\Model\User')->credentialsCreate(
            $req->get('u'),
            $req->get('p'),
            $req->get('e')
        );
        $login_res = $this->login($req);
        if ($login_res instanceof \Http\Response)
            return (new \Http\Response('success'))->setCookies(
                $login_res->getCookies()
            );
        else 
            return 'something goes wrong';
    }

    public function logout($req) {
        return (new \Http\Response('logged out'))->setCookie(
            'uac_token',
            'nope',
            time() - 72000,
            '/'
        );
    }

    public function login($req) {
        $user = $this->e->man('Uac\Model\User')->credentialsLogin(
            $req->get('u'),
            $req->get('p')
        );

        if ($user) {
            return $this->e->man('Uac\Model\User')->login(
                $user, 
                new \Http\Response('logged')
            );
        } else {
            return 'invalid';
        }
    }
}
