<?php
namespace Orm\Field;

class PrimaryKey extends Field {
    protected $config = array(
        'auto_increment' => true
    );

    public function __construct() {
        call_user_func_array('parent::__construct', func_get_args());
    }
}
