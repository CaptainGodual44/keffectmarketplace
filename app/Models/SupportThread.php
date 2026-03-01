<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class SupportThread extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'status',
    ];
}
