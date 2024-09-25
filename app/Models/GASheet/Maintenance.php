<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    public $table = 'maintenance_tx';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}