<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuelStation extends Model
{
  use HasFactory, SoftDeletes, SoftCascadeTrait;

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

}
