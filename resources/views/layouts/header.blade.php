<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keka Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .navbar {
            background-color: #2c3e50;
            padding: 10px 20px;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.8rem;
            color: #f39c12;
        }
        .nav-link {
            color: #ecf0f1;
            font-size: 1.1rem;
            margin-right: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-item.active .nav-link {
            color: #f39c12;
        }
        .btn-logout {
            border: 1px solid #f39c12;
            color: #f39c12;
            padding: 8px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-logout:hover {
            background-color: #f39c12;
            color: #fff;
        }
        .profile-pic {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f39c12;
        }
        .navbar-toggler {
            border-color: #f39c12;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
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
                        <li class="nav-item d-flex align-items-center">
                            <img src="{{ Auth::guard('admin')->user()->avatar ?? 'https://via.placeholder.com/45' }}" 
                                 alt="Profile Picture" class="profile-pic me-2">
                            <a class="btn btn-logout" href="{{ route('admin.logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
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
