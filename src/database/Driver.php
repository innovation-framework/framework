<?php
namespace App\Library\Database;

use App\Library\Database\File;
use App\Library\Database\Mysql;
use App\Library\Config;

/**
 * This class will driver application to store/read data
 * from file|mysql|sqlserver
 */
class Driver
{
    protected $configs = [];

    function __construct() {
        $configObj = new Config('database');
        if ($configObj instanceof Config) $this->configs = $configObj->get()->getObject();
    }

    /**
     * This function will drive connection to file|mysql|sqlserver|...
     * 
     * @param  string $table
     * 
     * @return File|Mysql|Sqlserver or something object
     */
    public function connection($table = '')
    {
        if (!$this->configs) return false;

        $driver = $this->configs->database->driver;
        if ($driver == 'file') {
            return new File($table, $this->configs->database);
        } else if ($driver == 'mysql') {
            return new Mysql($table, $this->configs->database);
        } else {
            return false;
        }
    }
}