@extends('layouts.app')

@section('content')
<h3>Edit Customer</h3>

<form method="POST" action="{{ route('customers.update', $customer->id) }}">
    @method('PUT')
    @include('customers.form')
</form>
@endsection
