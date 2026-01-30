<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Kashyap Investments') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="/dashboard">Kashyap Investments</a>
        <ul class="navbar-nav ms-auto">
            <li>
                <a href="{{ route('notifications') }}" class="nav-link position-relative">
                🔔
                @if(auth()->user()->unreadNotifications->count())
                <span class="badge bg-danger">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
                @endif
            </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="/tasks">Tasks</a></li>
            <li class="nav-item"><a class="nav-link" href="/customers">Customers</a></li>
            <li class="nav-item"><a class="nav-link" href="/events">Events</a></li>
            <li class="nav-item">
                <form method="POST" action="/logout">@csrf<button class="btn btn-sm btn-danger">Logout</button></form>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @yield('content')
</div>

</body>
</html>