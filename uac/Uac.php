<?php
namespace Uac;

class Uac extends \Core\Component {
    public function engage($e) {
        $this->e = $e;
        $e->router()->register('/uac/login/', function ($req) {
            $man = $this->e->man('Uac\Model\User');
            $man->credentialsLogin(
                $req->get('u'),
                $req->get('p')
            );
        });
    }
}
