<?php
namespace SRT\AIVisibility;

class ApiException extends \RuntimeException
{
    private $response;
    private $httpStatus;

    public function __construct($message, array $response = array(), $httpStatus = 0, $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->response = $response;
        $this->httpStatus = $httpStatus;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getHttpStatus()
    {
        return $this->httpStatus;
    }
}
