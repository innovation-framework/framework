<?php
namespace Light;

/**
 * This is request base class, will handler all request params
 */
class Request
{
    protected $get;
    protected $post;
    protected $headers;
    protected $cookies;

    function __construct()
    {
        $this->get = $_GET;

        $inputJSON = file_get_contents('php://input');
        $this->post = json_decode($inputJSON, TRUE);

        $this->headers = getallheaders();
        $this->cookies = $_COOKIE;
    }

    /**
     * This function will get all GET params
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->get;
    }

    /**
     * This function will get all POST params
     * 
     * @return array
     */
    public function postParams()
    {
        return $this->post;
    }

    /**
     * This function will get all HEADERS params
     * 
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * This is parent function to check request object is valid
     * Example:
     * + UserRequest is valid?
     * + ProductRequest is valid?
     * + and more
     * 
     * @return array
     */
    public function isValid()
    {
        return false;
    }
}