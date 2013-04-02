<?php
namespace Uac\Ex;

class UserExistException extends \Exception {
    public function __construct($username, $email) {
        parent::__construct('User with username ' . $username . ' or email ' . $email . ' already exists!');
    }
}
