<?php

namespace App\Providers;

use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\EloquentCategory;
use App\Repositories\Debt\DebtRepository;
use App\Repositories\Debt\EloquentDebt;
use App\Repositories\DebtHistory\DebtHistoryRepository;
use App\Repositories\DebtHistory\EloquentDebtHistory;
use App\Repositories\DebtProduct\DebtProductRepository;
use App\Repositories\DebtProduct\EloquentDebtProduct;
use App\Repositories\FuelStation\EloquentFuelStation;
use App\Repositories\FuelStation\FuelStationRepository;
use App\Repositories\InsuranceVehicle\EloquentInsuranceVehicle;
use App\Repositories\InsuranceVehicle\InsuranceVehicleRepository;
use App\Repositories\SubCategories\EloquentSubCategory;
use App\Repositories\SubCategories\SubCategoryRepository;
use App\Repositories\TractorDriver\EloquentTractorDriver;
use App\Repositories\TractorDriver\TractorDriverRepository;
use App\Repositories\Vehicle\EloquentVehicle;
use App\Repositories\Vehicle\VehicleRepository;
use Illuminate\Support\ServiceProvider;

class EloquentRepositoryProvider extends ServiceProvider
{
/**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
      $this->app->bind(CategoryRepository::class, EloquentCategory::class);
      $this->app->bind(SubCategoryRepository::class, EloquentSubCategory::class);
      $this->app->bind(DebtRepository::class, EloquentDebt::class);
      $this->app->bind(DebtHistoryRepository::class, EloquentDebtHistory::class);
      $this->app->bind(DebtProductRepository::class, EloquentDebtProduct::class);
      $this->app->bind(TractorDriverRepository::class, EloquentTractorDriver::class);
      $this->app->bind(VehicleRepository::class, EloquentVehicle::class);
      $this->app->bind(InsuranceVehicleRepository::class, EloquentInsuranceVehicle::class);
      $this->app->bind(FuelStationRepository::class, EloquentFuelStation::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
