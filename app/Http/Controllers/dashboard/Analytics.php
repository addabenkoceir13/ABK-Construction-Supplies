<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use Illuminate\Http\Request;
use Auth;

class Analytics extends Controller
{
  public function index()
  {
    $TotalDebt = Debt::getTotalDebt();
    $TotalPaidDebt = Debt::getTotalPaidDebt();
    $TotalRestDebt = Debt::getTotalRestDebt();
    $data = [
      'TotalDebt' => $TotalDebt,
      'TotalPaidDebt' => $TotalPaidDebt,
      'TotalRestDebt' => $TotalRestDebt,
    ];
    // return view('content.dashboard.dashboards-analytics');
    return view('content.dashboard.index', $data);
  }
}
