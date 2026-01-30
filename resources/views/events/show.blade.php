@extends('layouts.app')

@section('content')
<h1>Event #{{ $event->id }}</h1>
<p><strong>Customer:</strong> {{ $event->customer->name ?? '—' }}</p>
<p><strong>Type:</strong> {{ $event->type }}</p>
<p><strong>Date:</strong> {{ $event->event_date->format('Y-m-d') }}</p>
<p><strong>Note:</strong><br>{{ $event->note }}</p>
<p><strong>Notified:</strong> {{ $event->notified ? 'Yes' : 'No' }}</p>
<p>
    <a href="{{ route('events.edit', $event) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('events.index') }}" class="btn btn-secondary">Back</a>
</p>
@endsection
