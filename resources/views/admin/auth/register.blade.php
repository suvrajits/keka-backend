@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mt-5 shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h3>Admin Registration</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.register') }}">
                @csrf
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <div class="mb-3">
                    <label>Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
        </div>
    </div>
</div>
@endsection
