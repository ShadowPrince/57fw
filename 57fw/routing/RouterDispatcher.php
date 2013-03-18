<?php
namespace Routing;

/**
 * Class for router engaging
 */
class RouterDispatcher extends \Core\AppDispatcher {
    public function engage($e) {
        if ($this->config('engage_response'))
            return $e->router()->engageResponse();
        else {
            return $e->router()->engage($e->http()->getRequestPath());
        }
    }
}
