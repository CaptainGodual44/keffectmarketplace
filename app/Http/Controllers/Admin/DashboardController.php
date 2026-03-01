<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $metrics = [
            'orders_today' => 12,
            'revenue_l$' => 4520,
            'pending_reviews' => 4,
            'open_support_threads' => 7,
        ];

        return view('admin.dashboard', compact('metrics'));
    }
}
