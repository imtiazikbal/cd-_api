<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class AdminBaseController extends BaseController
{
    use AuthorizesRequests;

    use DispatchesJobs;

    use ValidatesRequests;

    protected function limit($default = 10): int
    {
        return (int) request()->input('limit', $default);
    }
}
