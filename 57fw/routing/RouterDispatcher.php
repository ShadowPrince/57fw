<?php
namespace Routing;

/**
 * Class for router engaging
 */
class RouterDispatcher extends \Core\AppDispatcher {
    public function engage($e) {
        if ($this->config('engage_response'))
            return $e->http->engageResponse();
        else {
            $e->http->setResponse(
                $e->router->engage(
                    $e->http->getRequest(),
                    $e->http->getRequest()->getRequestPath()
                )
            );
        }
    }
}
