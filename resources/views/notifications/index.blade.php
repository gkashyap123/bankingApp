@extends('layouts.app')

@section('content')
<h3>Notifications</h3>
<ul class="list-group">
@foreach($notifications as $n)
  <li class="list-group-item">
   <pre>Task Added: </pre> {{ $n->created_at->format('d M Y H:i') }}
  </li>
  
  <li class="list-group-item" style="background-color: #f8f9fa;">
    <?php // dd($n); ?>
    {{ $n->data['title'] ?? 'Notification' }}
  </li>
@endforeach
</ul>
@endsection
