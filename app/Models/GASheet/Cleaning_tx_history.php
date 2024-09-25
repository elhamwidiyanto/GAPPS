<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cleaning_tx_history extends Model
{
    use HasFactory;
  
    public $table = 'cleaning_tx_history';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}