<?php
namespace Orm\Ex;

class PkeyRequiredException extends \Orm\Ex\OrmException {
    public function __construct($action) {
        parent::__construct('Action "' . $action . '" requires model with primary key');
    }
}
