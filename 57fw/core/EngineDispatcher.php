<?php
namespace Core;

/** 
 * Interface for dispatchers
 */
interface EngineDispatcher {
    public function proceed($e);
}
