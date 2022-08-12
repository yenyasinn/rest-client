<?php

namespace RestClient;

final class ResponseData
{
    /** @var mixed */
    private $responseData;
    private string $responseBody;

    /**
     * @param mixed $responseData
     * @param string $responseBody
     */
    public function __construct($responseData, string $responseBody)
    {
        $this->responseData = $responseData;
        $this->responseBody = $responseBody;
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    /**
     * @return mixed
     */
    public function getResponseData()
    {
        return $this->responseData;
    }
}
