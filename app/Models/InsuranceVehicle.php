<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceVehicle extends Model
{
  use HasFactory, SoftDeletes, SoftCascadeTrait;

  protected $fillable = [
    'vehicle_id',
    'start_date',
    'end_date',
  ];

  public function vehicle()
  {
    return $this->belongsTo(Vehicle::class);
  }

  public function insuranceDateExpired()
  {
      return Carbon::today()->greaterThan(Carbon::parse($this->end_date));
  }

}
