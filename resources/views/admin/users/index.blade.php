@extends('layouts.app')

@section('content')
    <h1>Admin • Users</h1>
    <table>
        <thead><tr><th>User UUID</th><th>Role</th><th>Status</th></tr></thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user['uuid'] }}</td>
                <td>{{ $user['role'] }}</td>
                <td>{{ $user['status'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
