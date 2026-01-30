@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Tasks</h3>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">+ New Task</a>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Title</th>
            <th>Assigned To</th>
            <th>Status</th>
            <th>Due Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tasks as $task)
        <tr>
            <td>{{ $task->title }}</td>
            <td>{{ $task->assignee->name ?? '-' }}</td>
            <td>
                <span class="badge bg-info">{{ ucfirst($task->status) }}</span>
            </td>
            <td>
                {{ \Carbon\Carbon::parse($task->due_date)->format('d-m-Y') }}
            </td>


            <td>
                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-secondary">Edit</a>
                <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-info">View</a>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this task?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
