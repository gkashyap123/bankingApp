@extends('layouts.app')

@section('content')
<h3>Task Details</h3>
<div class="card mb-3">
    <div class="card-body">
        <h5>{{ $task->title }}</h5>
        <p>{{ $task->description }}</p>
        <p><strong>Assigned To:</strong> {{ $task->assignee->name ?? '-' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($task->status) }}</p>
        <p><strong>Due:</strong> 
        <!-- {{ $task->due_date?->format('d M Y') }} -->
    </p>

        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning">Edit</a>
        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-secondary">Back</a>

        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this task?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger">Delete</button>
        </form>
    </div>
</div>
@endsection
