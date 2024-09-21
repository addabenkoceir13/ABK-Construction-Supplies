<?php

namespace App\Providers;

use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\EloquentCategory;
use App\Repositories\Debt\DebtRepository;
use App\Repositories\Debt\EloquentDebt;
use App\Repositories\DebtProduct\DebtProductRepository;
use App\Repositories\DebtProduct\EloquentDebtProduct;
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
      $this->app->bind(DebtRepository::class, EloquentDebt::class);
      $this->app->bind(DebtProductRepository::class, EloquentDebtProduct::class);
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
