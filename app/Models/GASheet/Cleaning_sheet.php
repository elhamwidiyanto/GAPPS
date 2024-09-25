<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cleaning_sheet extends Model
{
    use HasFactory; 
  
    public $table = 'cleaning_header';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}