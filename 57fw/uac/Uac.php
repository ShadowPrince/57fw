<?php
namespace Uac;

class Uac extends \Core\Component {
    public function __construct($config=array()) {
        $this->config = array(
            'login_url' => function ($e) {
                return $e->router->make('uac.login');
            },
            'logged_url' => function ($e) {
                return '/index.php/test';
            },
        );
        parent::__construct($config);
    }

    public function engage($e) {
        $this->e = $e;

        $e->router->register('login/', array($this, 'login'), $this)
            ->validate('\Uac\Validators::notLogged');

        $e->router->register('logout/', array($this, 'logout'), $this)
            ->validate('\Uac\Validators::logged');

        $e->router->register('register/', array($this, 'register'), $this)
            ->validate('\Uac\Validators::notLogged');

        $this->cookieLogin();
    }

    protected function cookieLogin() {
        $man = $this->e->man('Uac\Model\User');
        $request = $this->e->http->getRequest();

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
        if ($req->user)  {
            return $this->e->man('Uac\Model\User')->logout(
                $req->user,
                new \Http\Response('logouted')
        );
        } else {
            return 'not logged';
        }
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
