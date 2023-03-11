<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class manualFacture extends Model
{
    protected $table = 'manualfactures';
    protected $guarded = [];
    use HasFactory;
}
