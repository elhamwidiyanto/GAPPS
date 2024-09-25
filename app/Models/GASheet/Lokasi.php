<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    public $table = 'master_lokasi';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
