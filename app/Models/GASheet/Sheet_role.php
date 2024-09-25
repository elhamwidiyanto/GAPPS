<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sheet_role extends Model
{
    use HasFactory;

    public $table = 'sheet_roles';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
