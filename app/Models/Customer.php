<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relationship: A customer can have many purchases
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}