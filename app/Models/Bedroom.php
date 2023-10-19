<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bedroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'quantity',
        'type_id',
        'accommodation_id'
    ];

    public function hotel(){
        return $this->belongsTo(Hotel::class);
    }

    public function type(){
        return $this->belongsTo(Type::class);
    }

    public function accommodation(){
        return $this->belongsTo(Accommodation::class);
    }
}
