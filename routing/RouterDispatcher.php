<?php
namespace Routing;

class RouterDispatcher implements \Core\EngineDispatcher {
    public function proceed($e) {
        return $e->router()->proceed($e->http()->getRequestPath());
    }
}
