<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Debt;
use App\Models\DebtProduct;
use App\Models\FuelStation;
use App\Models\SubCategory;
use App\Models\TractorDriver;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class Analytics extends Controller
{
    /**
     * Executive Business Intelligence & Analytics Dashboard
     */
    public function index()
    {
        // 1. Overall Debt Analytics
        $totalDebt = (float)Debt::sum('total_debt_amount');
        $totalPaidDebt = (float)Debt::sum('debt_paid');
        $totalRestDebt = (float)Debt::sum('rest_debt_amount');
        $recoveryRate = $totalDebt > 0 ? round(($totalPaidDebt / $totalDebt) * 100, 1) : 0;

        $totalDebtsCount = Debt::count();
        $unpaidDebtsCount = Debt::where('status', 'unpaid')->count();
        $paidDebtsCount = Debt::where('status', 'paid')->count();

        // Customer Debts (Tractor Driver ID = 1)
        $customerTotalDebt = (float)Debt::where('tractor_driver_id', 1)->sum('total_debt_amount');
        $customerPaidDebt = (float)Debt::where('tractor_driver_id', 1)->sum('debt_paid');
        $customerRestDebt = (float)Debt::where('tractor_driver_id', 1)->sum('rest_debt_amount');
        $customerUnpaidCount = Debt::where('tractor_driver_id', 1)->where('status', 'unpaid')->count();

        // Supplier Debts (Tractor Driver ID != 1)
        $supplierTotalDebt = (float)Debt::where('tractor_driver_id', '!=', 1)->sum('total_debt_amount');
        $supplierPaidDebt = (float)Debt::where('tractor_driver_id', '!=', 1)->sum('debt_paid');
        $supplierRestDebt = (float)Debt::where('tractor_driver_id', '!=', 1)->sum('rest_debt_amount');
        $supplierUnpaidCount = Debt::where('tractor_driver_id', '!=', 1)->where('status', 'unpaid')->count();

        // Top 5 Outstanding Customer Debts
        $topCustomerDebts = Debt::where('tractor_driver_id', 1)
            ->where('status', 'unpaid')
            ->orderByDesc('rest_debt_amount')
            ->limit(5)
            ->get();

        // Top 5 Outstanding Supplier Debts
        $topSupplierDebts = Debt::with('tractorDriver')
            ->where('tractor_driver_id', '!=', 1)
            ->where('status', 'unpaid')
            ->orderByDesc('rest_debt_amount')
            ->limit(5)
            ->get();

        // 2. Fuel Station Analytics
        $totalFuelAmount = (float)FuelStation::sum('amount');
        $totalPaidFuel = (float)FuelStation::where('status', 'paid')->sum('amount');
        $totalUnPaidFuel = (float)FuelStation::where('status', 'unpaid')->sum('amount');
        $totalLiters = (float)FuelStation::sum('liter');

        $dieselLiters = (float)FuelStation::where('type_fuel', 'diesel')->sum('liter');
        $dieselAmount = (float)FuelStation::where('type_fuel', 'diesel')->sum('amount');
        $gasolineLiters = (float)FuelStation::where('type_fuel', 'gasoline')->sum('liter');
        $gasolineAmount = (float)FuelStation::where('type_fuel', 'gasoline')->sum('amount');
        $gasLiters = (float)FuelStation::where('type_fuel', 'gas')->sum('liter');
        $gasAmount = (float)FuelStation::where('type_fuel', 'gas')->sum('amount');

        $fuelReceiptsCount = FuelStation::count();
        $fuelUnpaidReceiptsCount = FuelStation::where('status', 'unpaid')->count();

        // 3. Vehicle Fleet & Drivers Analytics
        $allVehicles = Vehicle::all();
        $totalVehicles = $allVehicles->count();
        $trucksCount = $allVehicles->where('type', 'truck')->count();
        $carsCount = $allVehicles->where('type', 'car')->count();
        $motoCount = $allVehicles->where('type', 'motorcycle')->count();
        $expiredInsuranceCount = $allVehicles->filter(fn($v) => $v->insuranceDateExpiredLast())->count();

        $totalDrivers = TractorDriver::where('id', '!=', 1)->count();
        $totalCategories = Category::count();
        $totalSubcategories = SubCategory::count();

        // 4. Chart Series
        $debtTimeline = Debt::getDebtTimeline();
        $fuelMonthly = FuelStation::getMonthlyFuelData();

        // Top Demanded Material Categories by Total Revenue
        $topProducts = DebtProduct::selectRaw('name_category, COUNT(*) as items_count, SUM(amount) as total_amount')
            ->groupBy('name_category')
            ->orderByDesc('total_amount')
            ->limit(6)
            ->get();

        return view('content.dashboard.index', compact(
            'totalDebt',
            'totalPaidDebt',
            'totalRestDebt',
            'recoveryRate',
            'totalDebtsCount',
            'unpaidDebtsCount',
            'paidDebtsCount',
            'customerTotalDebt',
            'customerPaidDebt',
            'customerRestDebt',
            'customerUnpaidCount',
            'supplierTotalDebt',
            'supplierPaidDebt',
            'supplierRestDebt',
            'supplierUnpaidCount',
            'topCustomerDebts',
            'topSupplierDebts',
            'totalFuelAmount',
            'totalPaidFuel',
            'totalUnPaidFuel',
            'totalLiters',
            'dieselLiters',
            'dieselAmount',
            'gasolineLiters',
            'gasolineAmount',
            'gasLiters',
            'gasAmount',
            'fuelReceiptsCount',
            'fuelUnpaidReceiptsCount',
            'totalVehicles',
            'trucksCount',
            'carsCount',
            'motoCount',
            'expiredInsuranceCount',
            'totalDrivers',
            'totalCategories',
            'totalSubcategories',
            'debtTimeline',
            'fuelMonthly',
            'topProducts'
        ));
    }

    /**
     * Alias method for backwards compatibility
     */
    public function index2()
    {
        return $this->index();
    }
}
