<?php
namespace App\Notepad;

class Urls implements \Routing\Urls {
    public function engage($e) {
        $e->router()->register('/', function ($e) {
            return 'IT WORKS <BR>';
        });
    }   
} 
