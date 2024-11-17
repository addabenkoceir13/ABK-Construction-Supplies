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

  public function insuranceDateExpired(): bool
  {
      return Carbon::today()->greaterThan(Carbon::parse($this->end_date));
  }
  public function insuranceDateExpiredLast(): bool
  {
      // Get the latest record based on the end_date
    $latestRecord = self::orderBy('end_date', 'desc')->first();
    if (!$latestRecord) {
        // No records found, return false or handle as needed
        return false;
    }

    // Check if the latest end_date is expired
    return Carbon::today()->greaterThan(Carbon::parse($latestRecord->end_date));
  }

}
