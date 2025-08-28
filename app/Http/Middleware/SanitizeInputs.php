<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class SanitizeInputs
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
        $inputs = $request->all();
        
        foreach($inputs as $key => $value) {
            if (is_string($value)) {
                $inputs[$key] = Purifier::clean($value, 'plain'); // Sanitize inputs
            }
        }
        //print_r($inputs); exit;

        $request->merge($inputs);

        //---------------------------------------------------------

        // $sanitized = collect($inputs)->map(function ($value, $key) {            
        //     return is_string($value) ? Purifier::clean($value, 'plain') : $value; // Sanitize inputs
        // })->toArray();
        // //print_r($sanitized); exit;
        // $request->merge($sanitized);

        return $next($request);
    }
}
