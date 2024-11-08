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
        'tractor_driver_id',
        'fullname',
        'phone',
        'date_debut_debt',
        'total_debt_amount',
        'debt_paid',
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

    public function tractorDriver()
    {
        return $this->belongsTo(TractorDriver::class, 'tractor_driver_id');
    }

    public static  function getTotalDebt()
    {
        return static::sum('total_debt_amount');
    }
    public static  function getTotalPaidDebt()
    {
        return static::sum('debt_paid');
    }

    public static  function getTotalRestDebt()
    {
        return static::sum('rest_debt_amount');
    }


}
