<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Product extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'description',
        'price_linden',
        'status',
    ];
}
