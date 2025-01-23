<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keka Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: #fff;
        }
        .nav-link {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 500;
        }
        .nav-link:hover {
            color: #f8f9fa;
        }
        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f8f9fa;
        }
        .nav-item.active .nav-link {
            color: #ffc107;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Keka Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item {{ request()->is('admin/profile') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.profile') }}">Profile</a>
                    </li>
                    <li class="nav-item {{ request()->is('admin/users') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.users') }}">Users</a>
                    </li>
                    <li class="nav-item {{ request()->is('admin/leaderboards') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.leaderboards') }}">Leaderboards</a>
                    </li>
                    @auth('admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                        <li class="nav-item">
                            <img src="{{ Auth::guard('admin')->user()->avatar ?? 'https://via.placeholder.com/40' }}" 
                                 alt="Profile Picture" class="profile-pic ms-2">
                        </li>
                    @else
                        <li class="nav-item {{ request()->is('admin/login') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.login') }}">Login</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
