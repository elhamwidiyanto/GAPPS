<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cleaning_tx extends Model
{
    use HasFactory;
  
    public $table = 'cleaning_tx';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
