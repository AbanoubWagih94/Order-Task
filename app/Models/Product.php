<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['user_id', 'category_id', 'title', 'image', 'description', 'price', 'price_before_discount', 'stock'];

    public function category() {
        return $this->belongsTo(Product::class, 'category_id');
    }
}
