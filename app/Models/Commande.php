<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $table = 'commandes';
    protected $primaryKey = 'id_commande';
    protected $guarded = [];
    protected $keyType = 'string'; // important in laravel 6+ 
    use HasFactory;
}
