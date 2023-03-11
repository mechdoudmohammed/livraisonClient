<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueFacture extends Model
{
    protected $table = 'historiquefactures';
    protected $guarded = [];
    use HasFactory;
}
