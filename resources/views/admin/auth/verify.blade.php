@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mt-5 shadow-lg">
        <div class="card-header bg-warning text-white text-center">
            <h3>Email Verification</h3>
        </div>
        <div class="card-body">
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                <script>
                    setTimeout(function(){
                        window.location.href = "{{ route('admin.login') }}";
                    }, 3000); // Redirect to login after 3 seconds
                </script>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.verify') }}">
                @csrf
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label>Verification Code</label>
                    <input type="text" class="form-control" name="verification_code" value="{{ old('verification_code') }}" required>
                </div>

                <button type="submit" class="btn btn-success w-100">Verify</button>
            </form>

            <hr>

            <form method="POST" action="{{ route('admin.resend_verification') }}">
                @csrf
                <input type="hidden" name="email" value="{{ old('email') }}">
                <button type="submit" class="btn btn-link w-100">Resend Verification Code</button>
            </form>
        </div>
    </div>
</div>
@endsection