<?php
namespace Routing;

/**
 * Class for router engaging
 */
class RouterEngageDispatcher extends \Core\AppDispatcher {
    public function __construct($config=array()) {
        parent::__construct($config);
    }

    public function engage($e) {
        return $e->http->engageResponse();
    }
}
