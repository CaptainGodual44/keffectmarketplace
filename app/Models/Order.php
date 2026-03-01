<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'total_linden',
    ];
}
