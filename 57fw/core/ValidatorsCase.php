<?php
namespace Core;

class ValidatorsCase extends AppDispatcher {
    protected static $e;

    public function engage($e) {
        self::$e = $e;
    }

}
