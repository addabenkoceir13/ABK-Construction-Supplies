<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class FuelStation extends Model
{
  use HasFactory, SoftDeletes, SoftCascadeTrait, Searchable;

  protected $fillable = [
    'vehicle_id',
    'name_owner',
    'name_driver',
    'name_distributor',
    'filing_datetime',
    'liter',
    'amount',
    'status',
    'type_fuel',
  ];


  public function vehicle()
  {
    return $this->belongsTo(Vehicle::class);
  }

  /**
   * Get the indexable data array for the model.
   *
   * @return array
   */
  public function toSearchableArray()
  {
    return [
      'name_owner'  => $this->name_owner,
      'name_driver' => $this->name_driver,
      'name_distributor' => $this->name_distributor,
      'liter'  => $this->liter,
      'amount' => $this->amount,
      'status' => $this->status,
      'type_fuel' => $this->type_fuel,
    ];
  }

  public static  function getTotalPaidFuel()
  {
    return static::where('status', 'paid')->sum('amount');
  }
  public static  function getTotalUnPaidFuel()
  {
    return static::where('status', 'unpaid')->sum('amount');
  }

  public static  function getTotalFuel()
  {
    return static::sum('amount');
  }

  public static  function getTotalLiter()
  {
    return static::sum('liter');
  }
  public static  function getTotalLiterTypeDiesl()
  {
    return static::where('type_fuel', 'diesel')->sum('liter');
  }

  public static  function getTotalLiterGas()
  {
    return static::where('type_fuel', 'gas')->sum('liter');
  }

  public static  function getTotalLiterGasoline()
  {
    return static::where('type_fuel', 'gasoline')->sum('liter');
  }

  public static  function getTotalAmountTypeDiesel()
  {
    return static::where('type_fuel', 'diesel')->sum('amount');
  }

  public static  function getTotalAmountGas()
  {
    return static::where('type_fuel', 'gas')->sum('amount');
  }

  public static  function getTotalAmountGasoline()
  {
    return static::where('type_fuel', 'gasoline')->sum('amount');
  }

  public static function getMonthlyFuelData()
  {
    return static::selectRaw('
        YEAR(filing_datetime) as year,
        MONTH(filing_datetime) as month,
        type_fuel,
        SUM(liter) as total_liters,
        SUM(amount) as total_amount
    ')
      ->groupBy('year', 'month', 'type_fuel')
      ->orderBy('year', 'asc')
      ->orderBy('month', 'asc')
      ->get();
  }
}
