@extends('layouts.app')
@include('layouts.header')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Admin - User Management</h1>
    
    <table class="table table-striped">
        <thead class="bg-primary text-white">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No users found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
