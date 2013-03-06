<?php
namespace Core;

/** 
 * Interface for dispatchers
 */
interface EngineDispatcher {
    /**
     * @param \Core\Engine
     */
    public function engage($e);
}
