<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    public $table = 'calendar';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}