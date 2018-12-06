<?php
namespace App\Library;

/**
 * This class will handle array
 */
class ArrayHandler extends \ArrayObject
{
    /**
     * This funcion will get array by key params
     * 
     * @param  array $keys
     * @return array
     */
    public function getArrayByKeys($keys=[])
    {
        $origin = $this->getArrayCopy();
        if (!$keys) return $origin;

        $results = [];
        array_walk($origin, function($value, $key) use ($keys, &$results) {
            if (in_array($key, $keys)) {
                $results[$key] = $value;
            }
        });
        
        return $results;
    }

    /**
     * This function will convert Object to Array
     * This one need to review and then remove it
     * just use objToArray in the future
     * 
     * @param  Object $obj
     * 
     * @return array
     */
    public function obj2Array($obj = null)
    {
        if (!$obj) return [];

        return json_decode(json_encode($obj), true);
    }

    /**
     * This function will convert Object to Array
     * 
     * @param  Object $obj
     * @param  Array &$arr
     * 
     * @return Array
     */
    public function objToArray($obj, &$arr)
    {
        if(!is_object($obj) && !is_array($obj)){
            $arr = $obj;
            return $arr;
        }

        foreach ($obj as $key => $value)
        {
            if (!empty($value)) {
                $arr[$key] = array();
                $this->objToArray($value, $arr[$key]);
            } else {
                $arr[$key] = $value;
            }
        }
        return $arr;
    }

    /**
     * This function will convert Array to Object
     * 
     * @param  Array  $arr
     * 
     * @return one or more stdClass Object in array
     */
    public function arr2Object($arr = [])
    {
        if (!$arr) return [];

        foreach ($arr as $key => $value) {
            $array[$key] = json_decode(json_encode($value));
        }

        return $array;
    }

    /**
     * This function will find all keys=>values map with conditions
     * 
     * @param  array  $conditions
     * 
     * @return array[stdClass object]
     */
    public function findByKeyValue($conditions = [])
    {
        $origin = $this->getArrayCopy();
        if (!$conditions|| !$origin) return [];


        $origin = $this->obj2Array($origin);
        $results = [];
        
        array_walk($origin, function($value, $key) use ($conditions, &$results) {
            $found = false;
            foreach ($conditions as $k => $v) {
                if (!isset($value[$k]) || !$value[$k]) {
                    $found = false;break;
                }

                $pos = strpos(strtolower($value[$k]), strtolower($v));
                if ($pos === false) {
                    $found = false;break;
                }
                $found = true;
            }
            if ($found) $results[] = $value;
        });

        return $this->arr2Object($results);
    }

    /**
     * This funtion will paging a array
     * 
     * @param  int $page
     * @param  int $pageSize
     * 
     * @return array
     */
    public function getOffsetPagesize($page = null, $pageSize = null)
    {
        $origin = $this->getArrayCopy();
        if (!$page || !$pageSize || !$origin) return $origin;

        if ($page == 1) return array_slice($origin, 0, $pageSize);
        if ($page == 2) return array_slice($origin, $pageSize, $pageSize);
        if ($page >= 3) return array_slice($origin, $pageSize * ($page - 1), $pageSize);
    }

    /**
     * This function will find and return values are mapping with find value
     * 
     * @param  array  $arr
     * @param  string $find
     * @return array
     */
    public function find($arr=[], $find='')
    {
        if (!$arr || !$find) return [];

        return array_filter($arr, function($element) use ($find)
        {
            if (strpos($element, $find)) return $element;
        });
    }
}