<?php
namespace Routing;

/**
 * Class for router engaging
 */
class RouterDispatcher extends \Core\AppDispatcher {
    public function engage($e) {
        if ($this->config('engage_response'))
            return $e->http()->engageResponse($e->router()->getResponse());
        else {
            return $e->router()->engage($e->http()->getRequestPath());
        }
    }
}
