<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonRetourClients extends Model
{
    protected $table = 'bonretourclients';
    protected $primaryKey = 'id_bon_retour_client';
    protected $keyType = 'string';
    protected $guarded = [];
    use HasFactory;
}
