@extends('template')

@section('contenu')
@foreach($data as $category)

<div class="category">
    <a href="{{ route('equipments.index', ['category' => $category['name']]) }}">
    <img class="category-img" src="{{ asset('/storage/images/'.$category['image_url']) }}">
    <span class="nom">{{ ucFirst($category["name"].'s') }}</span>
    </a>
</div>

<style scoped>
.category-img{
    max-height: 1000px;
    max-width:990px;
}

</style>

@endforeach
@endsection