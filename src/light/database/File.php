<?php
namespace Light\Database;

use Light\Database\DatabaseInterface;
use Light\Database\Database;
use Light\ArrayHandler;

/**
 * This class will handle to read/write to file
 */
class File extends Database implements DatabaseInterface
{
    // Name file
    protected $table;
    // This property will contain content of file
    protected $resource = [];
    // Path to file
    protected $path;
    // extension of file
    protected $fileType = 'json';

    function __construct($table = '', $configs = []) {
        $this->table = $table;
        $this->path = APP_ROOT . DIRECTORY_SEPARATOR . $configs->file->path;
        
        $this->setResource();
    }

    /**
     * This function will create connection to file
     * and set resource for object File
     */
    private function setResource()
    {
        if (file_exists($this->path . DIRECTORY_SEPARATOR . $this->table . '.' . $this->fileType)) {
            $string = file_get_contents($this->path . DIRECTORY_SEPARATOR . $this->table . '.' . $this->fileType);
            $this->resource = json_decode($string);
        } 
    }

    /**
     * This function will set created for object
     * that is storing in db
     * 
     * @param Array $new - prepare array before save into db
     * @param Object $obj - this is model object, example: UserModel, ProductModel
     */
    private function setCreated($new, $obj)
    {
        $new[$obj->getCreatedColumn()] = time();
        return $new;
    }

    /**
     * This function will set updated for object
     * that is storing in db
     * 
     * @param Array $new - prepare array before save into db
     * @param Object $obj - this is model object, example: UserModel, ProductModel
     */
    private function setUpdated($new, $obj)
    {
        $new[$obj->getUpdatedColumn()] = time();
        return $new;
    }

    /**
     * This funtion will append new content to file
     * 
     * @param  string $value
     * 
     * @return boolean
     */
    private function appendFile($value='')
    {
        try {
            $file = $this->path . DIRECTORY_SEPARATOR . $this->table . '.' . $this->fileType;
            $fileContents = file_get_contents($file);

            $value = $value . "\n" . ']';
            if(count(json_decode($fileContents)) > 0) $value = ',' . $value;


            $fh = fopen($file, "w");
            $fileContents = str_replace(']', $value, $fileContents);
            fwrite($fh, $fileContents);
            fclose($fh);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * This function to select a object with 
     * with some conditions, it haven't yet implement
     * will do in the future
     * 
     * @return object
     */
    public function select()
    {
        return false;
    }

    /**
     * This function will insert new record into file
     * 
     * @param  Object $obj - example UserModel, ProductModel
     * 
     * @return new object after insert success or false if fail
     */
    public function insert($obj)
    {
        try {
            $new = ['id' => $this->getMaxId() + 1];

            $objAttrs = $obj->getActiveAttr();
            foreach ($objAttrs as $attr) {
                $getFunction = 'get' . ucfirst($attr);
                $new[$attr] = $obj->{$getFunction}();
            }

            $new = $this->setCreated($new, $obj);
            $new = $this->setUpdated($new, $obj);

            return $this->appendFile(json_encode($new)) ? $new : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * This function will update record
     * that is found. This function haven't yet implemented,
     * need to implement in the feature
     * 
     * @return new object is updated or false if update fail
     */
    public function update()
    {
        return false;
    }

    /**
     * This function will delete record
     * that is found. This function haven't yet implemented,
     * need to implement in the feature
     * 
     * @return new object is deleted or false if delete is fail
     */
    public function delete()
    {
        return false;
    }

    /**
     * This function will find a object with key=>value
     * that was stored in file
     * 
     * @param  array  $keyValue
     * @return object or empty array if not found
     */
    public function findOneBy($keyValue = [])
    {
        if (!$keyValue || !$this->resource) return null;

        $arrayHandler = new ArrayHandler();
        foreach ($this->resource as $resource) {
            $originalResource = $resource;
            $resource = $arrayHandler->obj2Array($resource);

            $merged = array_merge($resource, $keyValue);
            if ($merged == $arrayHandler->obj2Array($originalResource)) return $originalResource;
        }
        return [];
    }

    /**
     * This function will find one or more result with
     * multi conditions
     * 
     * @param  array  $conditions
     * 
     * @return $this
     */
    public function findBy($conditions = [])
    {
        if (!$conditions) return null;

        $arrayHandler = new ArrayHandler($this->resource);
        $this->results = $arrayHandler->findByKeyValue($conditions);

        return $this;
    }

    /**
     * This function will get max id in file
     * 
     * @return int
     */
    public function getMaxId()
    {
        if ($this->resource) {
            $last = end($this->resource);
            return $last->id;
        }

        return 0;
    }
}