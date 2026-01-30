@extends('layouts.app')

@section('content')
<h1>Create Event</h1>
<form action="{{ route('events.store') }}" method="POST">
    @csrf
    @include('events._form')
    <button class="btn btn-primary">Create</button>
    <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
