<?php

namespace App\Http\Controllers;

use App\Services\JWTService;
use App\Traits\CommonTrait;
use App\Traits\CryptTrait;
use App\Traits\HttpResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, CommonTrait, CryptTrait, HttpResponses, ValidatesRequests;

    public function __construct(public JWTService $jWTService) {}
}
