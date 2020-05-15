<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;
use App\Exceptions\ApiException;

trait ApiTrait
{
    function badRequest($error): JsonResponse
    {
        throw new ApiException(['error' => $error], 400);    
    }
}
