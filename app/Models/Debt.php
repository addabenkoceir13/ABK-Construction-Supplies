<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait;

    protected $fillable = [
      'user_id',
      'supplier_id',
      'fullname',
      'phone',
      'date_debut_debt',
      'total_debt_amount',
      'rest_debt_amount',
      'date_end_debt',
      'status',
      'note',
    ];

    protected $softCascade = ['getDebtProduct'];

    public function getDebtProduct()
    {
        return $this->hasMany(DebtProduct::class);
    }
}
