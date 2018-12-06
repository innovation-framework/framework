<?php
//namespace App\Library\HttpFoundation;

use App\Library\HttpFoundation\BaseResponse;

/**
 * This class will handle JsonResponse Object
 */
class Json extends BaseResponse
{
    function __construct()
    {
        header('Content-Type: application/json');
    }

    /**
     * This function will set content for 
     * json respone object
     * 
     * @param array $content
     */
    public function setContent($content = [])
    {
        if (is_string($content)) $this->contents[] = $content;

        $this->contents[] = json_encode($content);
    }

    function __destruct() {
        if (count($this->contents) == 1) {
            echo $this->contents[0];
            die;
        }
        echo json_encode($this->contents);die;
    }
}