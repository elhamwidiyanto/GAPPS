<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pic extends Model
{
    use HasFactory;

    public $table = 'pic';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
