<?php

namespace App\Http\Controllers;

// for middleware
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

// abstract class Controller extends BaseController implements HasMiddleware { use AuthorizesRequests, DispatchesJobs, ValidatesRequests; }

abstract class Controller extends BaseController { use AuthorizesRequests, DispatchesJobs, ValidatesRequests; }
{
    //
}
