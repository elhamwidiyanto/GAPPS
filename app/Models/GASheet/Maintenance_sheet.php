<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance_sheet extends Model
{
    use HasFactory;

    public $table = 'maintenance_header';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
