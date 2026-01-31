<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relationship: A sale belongs to a Cashier (User)
    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship: A sale belongs to a Customer (optional)
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship: A sale has many items (Sale Details)
    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }
}