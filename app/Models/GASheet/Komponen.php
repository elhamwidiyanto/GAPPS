<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komponen extends Model
{
    use HasFactory;

    public $table = 'master_komponen';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
