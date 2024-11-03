<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
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


    public function fuelStations()
    {
      return $this->hasMany(FuelStation::class);
    }

    public function InsuranceVehicle()
    {
      return $this->hasMany(InsuranceVehicle::class);
    }
}
