@extends('template')
@section('contenu')
<div class="user-interactions">
        @foreach($data as $interactions)
        @foreach($interactions as $interaction)
        <span class="name"> {{ $interaction["name"] }} </span>
        @endforeach
        @endforeach
</div>
@endsection