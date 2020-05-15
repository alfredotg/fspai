<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    public $data;
    public $code;


    function __construct($data, int $code = 500)
    {
        $this->data = $data;
        $this->code = $code;
        parent::__construct(sprintf('ApiException (code: %d)', $code));
    }

    function render()
    {
        return response($this->data, $this->code);
    }
}
