<?php
namespace Light\Facade\Facades;
use Light\Facade\Facade;

/**
 * Log proxy
 */
class Log extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'log'; }
}