<?php
namespace Routing;

/**
 * Class for router engaging
 */
class RouterDispatcher extends \Core\AppDispatcher {
    public function __construct($config=array()) {
        parent::__construct($config);
    }

    public function engage($e) {
        $e->http->setResponse(
            $e->router->engage(
                $e->http->getRequest(),
                $e->http->getRequest()->getRequestPath()
            )
        );
    }
}
