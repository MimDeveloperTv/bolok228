<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mark extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    
     protected $fillable = [
        'title',
        'mark'
    ];
}
