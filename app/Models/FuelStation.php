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

}
