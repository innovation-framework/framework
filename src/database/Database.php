<?php
namespace App\Library\Database;
use App\Library\Config;
use App\Library\ArrayHandler;

/**
 * This class is common database class
 */
class Database
{
    protected $results;
    protected $offset;
    protected $pageSize;

    /**
     * This function will paging when you find data with conditions
     * in database
     * 
     * @param  integer $page
     * @return [Object]
     */
    public function paging($page = 1)
    {
        if (!$this->results) return []; 

        $configs = new Config('app');
        $configs = $configs->get()->getObject();
        $this->pageSize = $configs->app->pageSize;

        $arrayHandler = new ArrayHandler($this->results);

        return $arrayHandler->getOffsetPagesize($page, $this->pageSize);
    }

    /**
     * This function will get all results are found
     * 
     * @return [object]
     */
    public function getResults()
    {
        return $this->results;
    }
}