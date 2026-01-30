@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Customers</h3>
    <div>
        <a href="{{ route('customers.import') }}" class="btn btn-outline-secondary">Import CSV</a>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">+ New Customer</a>
        @if(auth()->user()->role === 'admin')
            <form action="{{ route('customers.destroyAll') }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Are you sure? This will permanently delete ALL customers and related events.')">
                @csrf
                <button class="btn btn-danger">Delete All</button>
            </form>
        @endif
    </div>
</div>
<table class="table table-striped">
<thead>
<tr>
    <th>Name</th>
    <th>Phone</th>
    <th>DOB</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
@foreach($customers as $customer)
<tr>
    <td>{{ $customer->name }}</td>
    <td>{{ $customer->phone }}</td>
    <td>{{ \Carbon\Carbon::parse($customer->dob)->format('d-m-Y') }}</td>
    <td>
        <a href="{{ route('customers.edit',$customer) }}" class="btn btn-sm btn-warning">Edit</a>
        <form method="POST" action="{{ route('customers.destroy',$customer) }}" class="d-inline">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger">Delete</button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>
@endsection