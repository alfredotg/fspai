<?php

namespace App\Http\Middleware;

use Closure;

class JsonInBody
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $json = $request->getContent();
        if(($data = @json_decode($json)))
        {
            foreach($data as $key => $value)
                $request[$key] = $value;
        }
        return $next($request);
    }
}
