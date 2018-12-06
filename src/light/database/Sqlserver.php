<?php
namespace App\Library\Database;

use App\Library\Database\DatabaseInterface;
use App\Library\Database\Database;

/**
 * This class will handle to read/write to Sqlserver
 */
class Sqlserver extends Database implements DatabaseInterface
{
    
    function __construct() {}

    public function select()
    {
        return false;
    }

    public function insert($obj)
    {
        return false;
    }

    public function update()
    {
        return false;
    }

    public function delete()
    {
        return false;
    }
}