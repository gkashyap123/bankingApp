@extends('layouts.app')

@section('content')
<h3>Import Customers</h3>
<p>You can upload a CSV file exported from Excel. The header row must contain at least: <code>name,email,phone,dob,anniversary,notes,investments</code>.</p>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card p-3">
    <form action="{{ route('customers.import.post') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">CSV File</label>
            <input type="file" name="file" accept=".csv" class="form-control" required>
            @error('file')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-primary">Upload and Import</button>
        <a class="btn btn-link" href="{{ route('customers.import.template') }}">Download sample template</a>
        <a class="btn btn-secondary" href="{{ route('customers.index') }}">Cancel</a>
    </form>
</div>
@endsection
