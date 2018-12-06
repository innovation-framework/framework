<?php
namespace App\Library\Facade\Facades;
use App\Library\Facade\Facade;

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