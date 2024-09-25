<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IT_sheet extends Model
{
    use HasFactory;

    public $table = 'it_header';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
