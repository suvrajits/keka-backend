<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .rank {
            font-size: 1.8rem;
            font-weight: bold;
            color: #007bff;
        }
        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Leaderboard</h1>
        <hr>

        @if (isset($error))
            <div class="alert alert-danger text-center">
                <strong>Error:</strong> {{ $error }}
            </div>
        @else
            <table class="table table-bordered table-striped">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Rank</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Total Score</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse ($leaderboard as $index => $entry)
                        <tr>
                            <td class="rank">{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ $entry['user']['avatar'] ?? 'https://via.placeholder.com/50' }}" 
                                     alt="Profile Picture" class="profile-pic">
                            </td>
                            <td>{{ $entry['user']['name'] }}</td>
                            <td>{{ $entry['user']['email'] }}</td>
                            <td>{{ $entry['score'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>