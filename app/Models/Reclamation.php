<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    protected $table = 'reclamations';
    protected $primaryKey = 'id_reclamation';
    protected $guarded = [];
    protected $keyType = 'string'; 
    use HasFactory;
}
