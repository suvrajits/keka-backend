@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-dark text-white text-center py-4">
                    <h2 class="fw-bold mb-0">Admin Profile</h2>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="{{ Auth::guard('admin')->user()->avatar ?? 'https://via.placeholder.com/150' }}" 
                             class="rounded-circle img-thumbnail shadow" alt="Profile Picture" width="150">
                        <h4 class="mt-3">{{ Auth::guard('admin')->user()->name }}</h4>
                        <p class="text-muted">Administrator</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-muted">Personal Information</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Name:</strong> {{ Auth::guard('admin')->user()->name }}</li>
                                <li class="list-group-item"><strong>Email:</strong> {{ Auth::guard('admin')->user()->email }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Account Details</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Joined:</strong> {{ Auth::guard('admin')->user()->created_at->format('d M Y') }}</li>
                                <li class="list-group-item"><strong>Last Updated:</strong> {{ Auth::guard('admin')->user()->updated_at->diffForHumans() }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary px-4 me-3">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.logout') }}" class="btn btn-outline-danger px-4"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
