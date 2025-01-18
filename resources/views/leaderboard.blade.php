<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Total Score</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaderboard as $index => $entry)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $entry['user']['name'] }}</td>
                            <td>{{ $entry['user']['email'] }}</td>
                            <td>{{ $entry['total_score'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
