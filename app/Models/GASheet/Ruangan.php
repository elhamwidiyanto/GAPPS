<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    public $table = 'master_ruangan';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
