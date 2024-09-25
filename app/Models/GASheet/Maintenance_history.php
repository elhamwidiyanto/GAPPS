<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance_history extends Model
{
    use HasFactory;

    public $table = 'maintenance_tx_history';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
