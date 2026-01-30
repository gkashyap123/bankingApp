@extends('layouts.app')

@section('content')
<h3>Edit Task</h3>

<form method="POST" action="{{ route('tasks.update', $task->id) }}">
    @csrf
    @method('PUT')

    @include('tasks.form', ['task' => $task])

</form>
@endsection
