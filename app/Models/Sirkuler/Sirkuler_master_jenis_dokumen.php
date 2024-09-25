<?php

namespace App\Models\Sirkuler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sirkuler_master_jenis_dokumen extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lara_sirkuler_master_jenis_dokumen';

    /**
     * fillable
     *
     * @var array
     */
    protected $guarded = [];
}
