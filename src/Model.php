<?php
namespace App\Library;
use App\Library\Database\Driver;

/**
 * This is base model class, will contain common perporties & functions in this one
 */
class Model
{
    protected $connection;
    protected $table;
    protected $createdColumn = 'created';
    protected $updatedColumn = 'updated';

    function __construct()
    {
        $this->setTable();
        $this->createConnection();
    }

    /**
     * This function will mapping between Model class with a table in database
     * Example
     * + UserModel will map with user table
     * + ProductModel will map with product table
     */
    private function setTable()
    {
        $childClass = get_called_class();
        $this->table = strtolower(end(explode(DIRECTORY_SEPARATOR, $childClass)));
    }

    /**
     * This function will create a connection into db after mapping table & model object
     *
     */
    private function createConnection()
    {
        $connection = new Driver();
        $this->connection = $connection->connection($this->table);
    }

    /**
     * This function is used to check Model object 
     * that is ready valid before save into database, 
     * developer need to override this fuction before use it
     * 
     * @return boolean
     */
    public function isValid()
    {
        return false;
    }

    /**
     * This function will get create timestamp of this record
     * that is inserted | updated into database
     * 
     * @return int
     */
    public function getCreatedColumn()
    {
        return $this->createdColumn;
    }

    /**
     * This function will get update timestamp of this record
     * that is inserted | updated into database
     * 
     * @return int
     */
    public function getUpdatedColumn() {
        return $this->updatedColumn;
    }
}