@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5 shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Admin Profile</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ Auth::guard('admin')->user()->avatar ?? 'https://via.placeholder.com/150' }}" 
                             class="rounded-circle img-thumbnail" alt="Profile Picture" width="150">
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>Name:</th>
                            <td>{{ Auth::guard('admin')->user()->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ Auth::guard('admin')->user()->email }}</td>
                        </tr>
                        <tr>
                            <th>Joined:</th>
                            <td>{{ Auth::guard('admin')->user()->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ Auth::guard('admin')->user()->updated_at->diffForHumans() }}</td>
                        </tr>
                    </table>
                    <div class="text-center mt-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                        <a href="{{ route('admin.logout') }}" class="btn btn-danger"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
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
