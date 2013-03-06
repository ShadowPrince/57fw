<?php
namespace Routing;

class RouterDispatcher implements \Core\EngineDispatcher {
    public function engage($e) {
        return $e->router()->engage($e->http()->getRequestPath());
    }
}
