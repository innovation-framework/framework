<?php
namespace Light\HttpFoundation;

/**
 * This class will handle response
 */
class BaseResponse
{
    // Type response
    protected static $types = ['json', 'xml'];
    // Headers response
    protected $headers = [];
    // Content response
    protected $contents = [];

    /**
     * This function will create new JsonResponse|XmlResponse Object
     * 
     * @param  string $class
     * 
     * @return JsonResponse|XmlResponse Object
     */
    public static function create($class = 'json')
    {
        $responseObj = new BaseResponse();

        $class = strtolower($class);
        if(!in_array($class, self::$types)) return $responseObj;

        $class = 'Light'. DIRECTORY_SEPARATOR. 'HttpFoundation'. DIRECTORY_SEPARATOR. ucfirst(strtolower($class));

        return new $class;
    }

    /**
     * This function will set headers for response
     * 
     * @param array $array
     */
    public function setHeader($array = []) 
    {
        if (!$array) return false;

        $key = key($array);
        header("$key: $array[$key]");
    }

    /**
     * This function will set content for
     * response, it will override in children class
     */
    public function setContent()
    {
    }
}
