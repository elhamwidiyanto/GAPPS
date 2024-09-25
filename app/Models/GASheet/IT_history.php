<?php

namespace App\Models\GASheet;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IT_history extends Model
{
    use HasFactory;

    public $table = 'it_tx_history';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
