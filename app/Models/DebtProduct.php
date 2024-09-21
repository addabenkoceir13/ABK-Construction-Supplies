<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebtProduct extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait;

    protected $fillable = [
        'debt_id',
        'name',
        'quantity',
        'amount',
        'total_amount',
        'status',
    ];

    public function getDebt()
    {
        return $this->belongsTo(Debt::class);
    }
}
