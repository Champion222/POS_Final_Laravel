<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $guarded = [];

    // 1:1 Relationship (Must implement)
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    // 1:M Relationship
    public function attendances() {
        return $this->hasMany(Attendance::class);
    }

    public function position() {
        return $this->belongsTo(Position::class);
    }
}