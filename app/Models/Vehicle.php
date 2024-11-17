<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait;

    protected $fillable = [
      'name',
      'type',
      'license_plate',
    ];

    protected $softCascade = ['insuranceVehicle', 'fuelStations'];

    public function fuelStations()
    {
      return $this->hasMany(FuelStation::class);
    }

    public function insuranceVehicle()
    {
      return $this->hasMany(InsuranceVehicle::class)->orderBy('end_date', 'desc');
    }

    public function insuranceDateExpiredLast(): bool
  {
      // Get the latest record based on the end_date
    $latestRecord = InsuranceVehicle::where('vehicle_id', $this->id)->orderBy('end_date', 'desc')->first();
    if (!$latestRecord) {
        // No records found, return false or handle as needed
        return false;
    }

    // Check if the latest end_date is expired
    return Carbon::today()->greaterThan(Carbon::parse($latestRecord->end_date));
  }
}
