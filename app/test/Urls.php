<?php
namespace App\Test;

class Urls implements \Core\AppUrls {
    public function register($e) {
        $e->router()->register('##', function ($e) {
            $tm = new Model\TestModel();
            $tm->login->setValue('1');
            $tm->password->setValue('1');
            echo $tm->id->getValue();
            $tmm = $e->manager(__NAMESPACE__ . '\Model\TestModel');
            $tmm->save($tm);
            return 'x';
        });
    }
}
