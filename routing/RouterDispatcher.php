<?php
namespace Routing;

class RouterDispatcher {
    public function proceed($e) {
        return $e->router()->proceed($e->http()->getRequestPath());
    }
}
