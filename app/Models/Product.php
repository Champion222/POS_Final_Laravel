<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = []; // Allows mass assignment
    public function promotions() {
        return $this->belongsToMany(Promotion::class, 'product_promotion');
    }
    // Relationship: A product belongs to a Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship: A product belongs to a Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relationship: A product has many Sale Details
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }
}