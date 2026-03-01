<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

Artisan::command('marketplace:reset-admin {email=admin@example.com} {password=password}', function (string $email, string $password): void {
    $user = User::query()->updateOrCreate(
        ['email' => $email],
        [
            'public_uuid' => (string) Str::uuid(),
            'name' => 'Admin User',
            'password' => Hash::make($password),
            'role' => 'admin',
            'status' => 'active',
        ],
    );

    $this->info('Admin account is ready.');
    $this->line('Email: '.$user->email);
    $this->line('Password: '.$password);

    Log::info('Admin account reset via CLI command.', ['email' => $user->email]);
})->purpose('Create/reset admin credentials for marketplace login troubleshooting');
