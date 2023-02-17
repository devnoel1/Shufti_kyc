<?php

namespace App\Http\Controllers;

use App\Helpers\Kyc;
use Illuminate\Http\Request;

class kycController extends Controller
{
    function makeVerification(Request $request)
    {
        $kyc = Kyc::verifyDocument();

        return response()->json(['kyc_url'=>$kyc]);
    }
}
