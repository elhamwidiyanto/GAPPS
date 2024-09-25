<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitiesLog extends Model
{
    use HasFactory;

    public $table = 'lara_activities_log';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
