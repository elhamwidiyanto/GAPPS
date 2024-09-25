<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    use HasFactory;

    public $table = 'master_gedung';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
