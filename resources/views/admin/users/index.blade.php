@extends('layouts.app')

@section('content')
    <h1>Admin • Users</h1>
    <table>
        <thead><tr><th>User UUID</th><th>Email</th><th>Role</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($users as $user)
            <tr>
                <td>{{ $user->public_uuid }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>{{ $user->status }}</td>
            </tr>
        @empty
            <tr><td colspan="4">No users found.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">
        {{ $users->links() }}
    </div>
@endsection
