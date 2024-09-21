<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait;

    protected $fillable = ['user_id', 'fullname', 'number'];

    protected $softCascade = ['getDebtProduct'];

    public function getDebtProduct()
    {
        return $this->hasMany(DebtProduct::class);
    }
}
