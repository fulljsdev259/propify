<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Config;
use Session;

class Locale
{
    public function handle($request, Closure $next)
    {                
        if($request->header('Localization')){        
            App::setLocale($request->header('Localization'));
        }        
        elseif ($request->user() && $request->user()->settings) {
            App::setLocale($request->user()->settings->language);
        }

//        App::setLocale('en');
        return $next($request);
    }
 }
