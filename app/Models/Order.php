<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'lat',
        'lon',
        'address',
        'zipcode',
        'ext_num',
        'int_num',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
