<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_type extends Model
{
    use HasFactory;
 
    public $table = 'master_type';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}