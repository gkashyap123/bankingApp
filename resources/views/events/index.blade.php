@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h1>Events</h1>
    <a href="{{ route('events.create') }}" class="btn btn-primary">Create Event</a>
</div>
<table class="table table-bordered">
    <thead><tr><th>#</th><th>Customer</th><th>Type</th><th>Date</th><th>Notified</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($events as $event)
        <tr>
            <td>{{ $event->id }}</td>
            <td>{{ $event->customer->name ?? '—' }}</td>
            <td>{{ $event->type }}</td>
            <td>{{ $event->event_date->format('Y-m-d') }}</td>
            <td>{{ $event->notified ? 'Yes' : 'No' }}</td>
            <td>
                <a href="{{ route('events.show',$event) }}" class="btn btn-sm btn-secondary">View</a>
                <a href="{{ route('events.edit',$event) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('events.destroy',$event) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this event?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="6">No events found.</td></tr>
    @endforelse
    </tbody>
</table>

{{ $events->links() }}
@endsection
