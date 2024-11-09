<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait;
    protected $table = 'categories';
    protected $fillable = [
      'name',
    ];

    protected $softCascade = ['getSubcategories'];

    public function getSubcategories()
    {
      return $this->hasMany(SubCategory::class);
    }

}
