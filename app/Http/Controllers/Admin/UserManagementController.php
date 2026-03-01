<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

final class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->latest('id')
            ->paginate(25);

        return view('admin.users.index', compact('users'));
    }
}
