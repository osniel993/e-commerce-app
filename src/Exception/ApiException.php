<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    protected array $errorList = [];

    public function __construct(string $message = null, int $statusCode = 400, \Exception $previous = null, array $headers = [], ?int $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * @return array
     */
    public function getErrorList(): array
    {
        return $this->errorList;
    }

    /**
     * @param array $errorList
     */
    public function setErrorList(array $errorList): void
    {
        $this->errorList = $errorList;
    }
}
