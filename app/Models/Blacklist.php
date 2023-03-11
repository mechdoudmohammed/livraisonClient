<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class blacklist extends Model
{
    protected $table = 'blacklists';
    protected $guarded = [];
    use HasFactory;
}
