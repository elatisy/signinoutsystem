<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SignInSystem\Hub;

class SignInController extends Controller
{
    public function handle(Request $request){
        $hub = new Hub();
        return response($hub->handle($request->all()));
    }
}
