@extends('template')
@section('contenu')
<h2>List of equipments</h2>
<div class="equipments"> 
    @foreach($data as $equipment)
    <div class="equipment" data-available="{{ $equipment["availability"] }}">
        <img style="max-width: 200px" src="{{ asset('/storage/images/'.$equipment['image_url']) }}">
        <span class="name"> {{ $equipment["name"]}} </span>
        <span class="description"> {{ $equipment["description"]}}</span>
        <span class="availability">{{ $equipment["availability"] }}</span>
        @if($equipment["reservation"] != null)
            <span class="reservation"> 
                Reserved from: {{ \Carbon\Carbon::parse($equipment["reservation"]["start"])->format('l j F') }}
                 to {{ \Carbon\Carbon::parse($equipment["reservation"]["end"])->format('l j F') }}
            </span>
            @can('isAdmin')
                <span style="background-color:green">by {{ $equipment["reservation"]["username"] }}</span>
            @endcan
        @endif
        @if($equipment["borrow"] != null)
            @can('isAdmin')
                <span class="borrow"> Borrowed since: {{ \Carbon\Carbon::parse($equipment["borrow"]["start"])->format('l j F') }}</span>
                <span style="background-color:red">by {{ $equipment["borrow"]["username"] }}</span>
            @endcan
        @endif
        @if($equipment["reservation"] == null)
            <form class="reserve-form" action="{{ route('reserve') }} " method="POST">
                @csrf
                @method('POST')
                <input style="display_none"  type="hidden" name="equipment_id" value="{{ $equipment["id"] }}" disabled>
                <input type="submit" class="reserve-button" value="reserve">
            </form>
        @else
            @can('isAdmin')
                <form class="reserve-form" action="{{ route('reserve.accept') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="submit" class="accept-reserve-button" value="accept reservation">
                </form>
                <form class="reserve-form" action="{{ route('reserve.cancel') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="submit" class="cancel-reserve-button" value="cancel reservation">
                </form>
            @endcan
        @endif    
        @can('isAdmin')
            @if($equipment["borrow"] == null)
                <form class="borrow-form" action="{{ route('borrow.start') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="submit" class="borrow-start-button" value="borrow">
                </form>
            @else
            <form class="borrow-form" action="{{ route('borrow.end') }}" method="POST">
                @csrf
                @method('POST')
                <input type="submit" class="borrow-end-button" value="confirm delivery">
            </form>
            @endif
        @endcan
    </div>
    @endforeach
</div>
@endsection