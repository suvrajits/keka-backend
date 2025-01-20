<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .leaderboard-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .leaderboard-title {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }
        .rank {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
            text-align: center;
        }
        .profile-pic {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        .table thead {
            background-color: #007bff;
            color: #ffffff;
            font-weight: bold;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="leaderboard-container">
        <h1 class="leaderboard-title">🏆 Leaderboard</h1>
        <hr>

        @if (isset($error))
            <div class="alert alert-danger text-center">
                <strong>Error:</strong> {{ $error }}
            </div>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr class="text-center">
                        <th>Rank</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Total Score</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaderboard as $entry)
                        <tr class="text-center">
                            <td class="rank">{{ $entry['rank'] }}</td>
                            <td>
                                <img src="{{ $entry['avatar'] ?? 'https://via.placeholder.com/60' }}" 
                                     alt="Profile Picture" class="profile-pic">
                            </td>
                            <td>{{ $entry['name'] }}</td>
                            <td>{{ $entry['email'] }}</td>
                            <td class="fw-bold">{{ number_format($entry['score']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="no-data">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
