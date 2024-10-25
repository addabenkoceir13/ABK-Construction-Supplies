<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait;
    protected $table = 'subcategories';
    protected $fillable = [
      'category_id',
      'name',
      'input_type',
    ];

    public function  getCategory(): BelongsTo
    {
      return $this->belongsTo(Category::class, 'category_id');
    }

    public function getDebtProducts()
    {
      return $this->hasMany(DebtProduct::class);
    }
}
