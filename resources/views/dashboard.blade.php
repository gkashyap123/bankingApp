@extends('layouts.app')
@section('content')
<h3>Dashboard</h3>

<div class="row">
    <div class="col-md-3">
        <div class="card text-bg-primary mb-3">
            <div class="card-body">
                <h5>Total Tasks</h5>
                <h2>{{ $taskCount }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success mb-3">
            <div class="card-body">
                <h5>Completed</h5>
                <h2>{{ $completedTasks }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning mb-3">
            <div class="card-body">
                <h5>Pending</h5>
                <h2>{{ $pendingTasks }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info mb-3">
            <div class="card-body">
                <h5>Today's Birthdays</h5>
                <h2>{{ $todayBirthdays }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-body">
                <h5>Upcoming Events ({{ $upcomingEventsCount }}) <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-primary float-end">View All</a></h5>

                @if($upcomingEvents->isEmpty())
                    <p>No upcoming events.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($upcomingEvents as $event)
                            <li class="list-group-item">
                                <strong>{{ $event->event_date->format('Y-m-d') }}</strong> - {{ $event->type }}
                                <div class="small text-muted">Customer: {{ $event->customer->name ?? '—' }} <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-link">View</a></div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-body">
                <h5>Upcoming Anniversaries ({{ $upcomingAnniversariesCount }})</h5>

                @if($upcomingAnniversaries->isEmpty())
                    <p>No upcoming anniversaries.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($upcomingAnniversaries as $c)
                            <li class="list-group-item">
                                <strong>{{ $c->anniversary->format('m-d') }}</strong> - {{ $c->name }}
                                <div class="small text-muted">Email: {{ $c->email ?? '—' }}</div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection