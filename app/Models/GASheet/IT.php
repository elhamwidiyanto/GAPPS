<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IT extends Model
{
    use HasFactory;

    public $table = 'it_tx';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}