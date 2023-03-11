<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $primaryKey = 'id_facture';
    protected $table = 'factures';
    protected $keyType = 'string';
    protected $guarded = [];
    use HasFactory;
}
