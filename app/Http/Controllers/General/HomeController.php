<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\MerchantBaseController;

class HomeController extends MerchantBaseController
{
    /**
     * Landing Page
     *
     */
    public function index()
    {
        return view('landing.index');
    }

    public function thankYou()
    {
        return view('landing.thank_you');
    }
}
