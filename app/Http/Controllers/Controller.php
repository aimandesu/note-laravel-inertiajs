<?php

namespace App\Http\Controllers;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use ValidatesRequests, ApiResponse;
}
