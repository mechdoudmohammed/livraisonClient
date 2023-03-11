<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueArticle extends Model
{
    protected $table = 'historiquearticles';
    protected $guarded = [];
    use HasFactory;
}
