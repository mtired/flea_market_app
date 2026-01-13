<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_user_id',
        'item_id',
        'postal_code',
        'address',
        'building',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
