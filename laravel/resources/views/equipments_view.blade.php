@extends('template')
@section('contenu')
<h2>{{ var_dump($data) }}</h2>
<div class="equipments"> 
    @foreach($data as $equipment)
    <div class="equipment" style="background-color:green">
        <img style="max-width: 200px" src="{{ asset('/storage/images/'.$equipment['image_url']) }}">
        <span class="name"> {{ $equipment["name"]}} </span>
        <span class="description"> {{ $equipment["description"]}} </span>
    </div>
    @endforeach
</div>
@endsection