<?php
namespace App\Notepad;

class Urls implements \Routing\Urls {
    public function register($e) {
        $e->router()->register('##', function ($e) {
            $man = $e->manager(__NAMESPACE__ . '\Model\Note');

            foreach ($man->find(array(
                '`cat` = %s' => array($e->manager(new Model\NoteCat)->get(1)->id)
            )) as $i) {
                break;
            }
        });
    }   
} 
