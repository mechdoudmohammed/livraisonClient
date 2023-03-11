<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaire extends Model
{
    protected $table = 'inventaires';
    protected $guarded = [];
    use HasFactory;
}
