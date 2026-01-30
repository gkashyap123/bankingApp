@extends('layouts.app')

@section('content')
<h3>Add Customer</h3>

<form method="POST" action="{{ route('customers.store') }}">
    @csrf

    <div class="mb-3">
        <label>Name</label>
        <input name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Phone</label>
        <input name="phone" class="form-control">
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input name="email" class="form-control">
    </div>

    <div class="mb-3">
        <label>Date of Birth</label>
        <input type="date" name="dob" class="form-control">
    </div>

    <div class="mb-3">
        <label>Notes</label>
        <textarea name="notes" class="form-control"></textarea>
    </div>

    <button class="btn btn-success">Save Customer</button>
</form>
@endsection
