<?php
namespace Routing;

class RouterDispatcher extends \Core\EngineDispatcher {
    public function engage($e) {
        return $e->router()->engage($e->http()->getRequestPath());
    }
}
