<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';
    protected $primaryKey = 'id_article';
    protected $guarded = [];
    use HasFactory;
    protected $keyType = 'string'; // important in laravel 6+ 

}
