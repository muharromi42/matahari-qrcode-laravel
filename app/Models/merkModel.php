<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class merkModel extends Model
{
    use HasFactory;

    protected $table = "tbl_merk";
    protected $fillable = [
        'merk',
        'catatan',
    ];
}
