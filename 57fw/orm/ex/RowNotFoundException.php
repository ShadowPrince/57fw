<?php
namespace Orm\Ex;

class RowNotFoundException extends \Orm\Ex\OrmException {
    public function __construct($search) {
        parent::__construct('Row not found: ' . $search);
    }
}
