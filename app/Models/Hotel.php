<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'direction',
        'city',
        'nit',
        'number_rooms'
    ];

    public function bedrooms(){
        return $this->hasMany(Bedroom::class);
    }
}
