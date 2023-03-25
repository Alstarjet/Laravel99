<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;


class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'size',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
