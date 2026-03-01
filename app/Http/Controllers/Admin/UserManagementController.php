<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = [
            ['uuid' => '11111111-1111-1111-1111-111111111111', 'role' => 'customer', 'status' => 'active'],
            ['uuid' => '22222222-2222-2222-2222-222222222222', 'role' => 'vendor', 'status' => 'active'],
        ];

        return view('admin.users.index', compact('users'));
    }
}
