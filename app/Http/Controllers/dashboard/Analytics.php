<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\FuelStation;
use Illuminate\Http\Request;
use Auth;

class Analytics extends Controller
{
  public function index()
  {
    $TotalDebt = Debt::getTotalDebt();
    $TotalPaidDebt = Debt::getTotalPaidDebt();
    $TotalRestDebt = Debt::getTotalRestDebt();

    $TotalPaidFuel    = FuelStation::getTotalPaidFuel();
    $TotalUnPaidFuel  = FuelStation::getTotalUnPaidFuel();
    $TotalFuel        = FuelStation::getTotalFuel();
    $TotalLiter       = FuelStation::getTotalLiter();
    $getTotalLiterTypeDiesl   = FuelStation::getTotalLiterTypeDiesl();
    $TotalLiterGas    = FuelStation::getTotalLiterGas();
    $TotalLiterGasoline     = FuelStation::getTotalLiterGasoline();
    $TotalAmountTypeDiesel  = FuelStation::getTotalAmountTypeDiesel();
    $TotalAmountGas         = FuelStation::getTotalAmountGas();
    $TotalAmountGasoline    = FuelStation::getTotalAmountGasoline();

    $data = [
      'TotalDebt' => $TotalDebt,
      'TotalPaidDebt' => $TotalPaidDebt,
      'TotalRestDebt' => $TotalRestDebt,

      'TotalPaidFuel'   => $TotalPaidFuel,
      'TotalUnPaidFuel' => $TotalUnPaidFuel,
      'TotalFuel'       => $TotalFuel,
      'TotalLiter'      => $TotalLiter,
      'getTotalLiterTypeDiesl'  => $getTotalLiterTypeDiesl,
      'TotalLiterGas'   => $TotalLiterGas,
      'TotalLiterGasoline'      => $TotalLiterGasoline,
      'TotalAmountTypeDiesel'   => $TotalAmountTypeDiesel,
      'TotalAmountGas'          => $TotalAmountGas,
      'TotalAmountGasoline'     => $TotalAmountGasoline,
    ];

    $data['debtTimeline'] = Debt::getDebtTimeline();
    // $data['driverDebts']  = Debt::getDriverDebts();
    $data['fuelMonthly'] = FuelStation::getMonthlyFuelData();
    // return view('content.dashboard.dashboards-analytics');
    return view('content.dashboard.index', $data);
  }
  public function index2()
  {
    $TotalDebt = Debt::getTotalDebt();
    $TotalPaidDebt = Debt::getTotalPaidDebt();
    $TotalRestDebt = Debt::getTotalRestDebt();

    $TotalPaidFuel    = FuelStation::getTotalPaidFuel();
    $TotalUnPaidFuel  = FuelStation::getTotalUnPaidFuel();
    $TotalFuel        = FuelStation::getTotalFuel();
    $TotalLiter       = FuelStation::getTotalLiter();
    $getTotalLiterTypeDiesl   = FuelStation::getTotalLiterTypeDiesl();
    $TotalLiterGas    = FuelStation::getTotalLiterGas();
    $TotalLiterGasoline     = FuelStation::getTotalLiterGasoline();
    $TotalAmountTypeDiesel  = FuelStation::getTotalAmountTypeDiesel();
    $TotalAmountGas         = FuelStation::getTotalAmountGas();
    $TotalAmountGasoline    = FuelStation::getTotalAmountGasoline();

    // New debt timeline data
    $debtTimeline = Debt::selectRaw('YEAR(date_end_debt) as year, MONTH(date_end_debt) as month, SUM(total_debt_amount) as total')
        ->whereNotNull('date_end_debt')
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();


    // New fuel timeline data
    $fuelTimeline = FuelStation::selectRaw('YEAR(filing_datetime) as year, MONTH(filing_datetime) as month, SUM(liter) as liters, SUM(amount) as amount')
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

    $data = [
      'TotalDebt' => $TotalDebt,
      'TotalPaidDebt' => $TotalPaidDebt,
      'TotalRestDebt' => $TotalRestDebt,

      'TotalPaidFuel'   => $TotalPaidFuel,
      'TotalUnPaidFuel' => $TotalUnPaidFuel,
      'TotalFuel'       => $TotalFuel,
      'TotalLiter'      => $TotalLiter,
      'getTotalLiterTypeDiesl'  => $getTotalLiterTypeDiesl,
      'TotalLiterGas'   => $TotalLiterGas,
      'TotalLiterGasoline'      => $TotalLiterGasoline,
      'TotalAmountTypeDiesel'   => $TotalAmountTypeDiesel,
      'TotalAmountGas'          => $TotalAmountGas,
      'TotalAmountGasoline'     => $TotalAmountGasoline,

      // New chart data
        'debtTimeline' => $debtTimeline,
        'fuelTimeline' => $fuelTimeline,
        'fuelTypes' => [
            'diesel' => $getTotalLiterTypeDiesl,
            'gasoline' => $TotalLiterGasoline,
            'gas' => $TotalLiterGas
        ],
        'paymentStatus' => [
            'paid' => $TotalPaidFuel,
            'unpaid' => $TotalUnPaidFuel
        ]
    ];


    // return view('content.dashboard.dashboards-analytics');
    return view('content.dashboard.index2', $data);
  }
}
