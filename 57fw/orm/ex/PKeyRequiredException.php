<?php
namespace Orm\Ex;

class PkeyRequiredException extends OrmException {
    public function __construct($action) {
        parent::__construct('Action "' . $action . '" requires model with primary key');
    }
}
