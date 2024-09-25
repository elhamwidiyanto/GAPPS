<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    public $table = 'alat';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}