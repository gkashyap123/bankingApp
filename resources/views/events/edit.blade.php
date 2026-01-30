@extends('layouts.app')

@section('content')
<h1>Edit Event</h1>
<form action="{{ route('events.update', $event) }}" method="POST">
    @csrf
    @method('PUT')
    @include('events._form')
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
