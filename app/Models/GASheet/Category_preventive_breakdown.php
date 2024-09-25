<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category_preventive_breakdown extends Model
{
    use HasFactory;

    public $table = 'category_preventive_breakdown';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}