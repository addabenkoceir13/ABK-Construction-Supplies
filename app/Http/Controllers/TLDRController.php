<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TLDRController extends Controller
{
  public function index()
  {
    return view('content.dashboard.temp');
  }

  public function action(Request $request){
    dd($request);
  }
}
