@extends('template')
@section('contenu')
    <h2>List of reservations</h2>
    <div class="filter">
        <form class="filter_form" action="/" method="GET">
            <label for="equipment">Equipment</label>
            <select class="equipment_id">
                @foreach ($data['equipments'] as $eq)
                    <option value="{{ $eq['id'] }}">{{ $eq['name'] }}</option>
                @endforeach
            </select>
            <label for="from-label">From</label>
            <input class="from-input" type="date" value="{{ Carbon\Carbon::now() }}">
            <label for="to-label">To</label>
            <input class="to-input" type="date" value="{{ Carbon\Carbon::now()->addDay(1) }}">
            <div class="status-filter">
                <label>Show only equipments with status:</label>
                <label for="status-filter-all">All</label>
                <input class="status-filter-radio" type="radio" value="all" name="status-filter">
                <label for="status-filter-all">Unvalidated</label>
                <input class="status-filter-radio" type="radio" value="unvalidated" name="status-filter">
                <label for="status-filter-validated">Validated</label>
                <input class="status-filter-radio" type="radio" value="validated">
                <label for="status-filter-cancelled">Cancelled</label>
                <input class="status-filter-radio" type="radio" value="cancelled">
            </div>
        </form>
    </div>
    <div class="panel panel-info">
        {{-- Equipments list --}}
        @foreach ($data['reservations'] as $reservation)
            <div class="reservation" data-currentlyRunning="{{ $reservation['currently_running'] }}"
                data-awaintingValidation="{{ $reservation['awaiting_validation'] }}"
                data-cancelled="{{ $reservation['cancelled'] }}" $data-userId="{{ $reservation['user_id'] }}"
                $data-equipmentId="{{ $reservation['equipment_id'] }}" $data-start="{{ $reservation['start'] }}"
                $data-end="{{ $reservation['end'] }}">
                <img style="max-width: 50px" src="{{ asset('/storage/images/' . $reservation['equ_img_url']) }}">
                <span class="name"> {{ $reservation['equ_name'] }} </span>
                <span class="start"> From {{ \Carbon\Carbon::parse($reservation['start'])->format('l j F Y') }}
                    to {{ \Carbon\Carbon::parse($reservation['end'])->format('l j F Y') }}
                </span>
                @if ($reservation['awaiting_validation'] && !$reservation['cancelled'])
                    <span class="status"> Unvalidated </span>
                    {{-- Accepntance form for admin --}}
                    @can('isAdmin')
                        <form class="reserve-form" action="{{ route('reserve.accept') }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="form-group {!! $errors->has('equipment_is_not_reservable') ? 'haserror' : '' !!}">
                                {!! $errors->first('equipment_is_not_reservable', '<small class="helpblock">:message</small>') !!}
                            </div>
                            <div class="form-group {!! $errors->has('equipment_id') ? 'haserror' : '' !!}">
                                <input type="hidden" name="equipment_id" value="{{ $reservation['equipment_id'] }}">
                                {!! $errors->first('equipment_id', '<small class="helpblock">:message</small>') !!}
                            </div>
                            <div class="form-group {!! $errors->has('user_id') ? 'haserror' : '' !!}">
                                <input type="hidden" name="user_id" value="{{ $reservation['user_id'] }}">
                                {!! $errors->first('user_id', '<small class="helpblock">:message</small>') !!}
                            </div>
                            <input type="hidden" name="id" value="{{ $reservation['id'] }}">
                            <input type="submit" class="accept-reserve-button" value="accept reservation">
                        </form>
                    @endcan
                @elseif($reservation['cancelled'])
                    <span class="status"> Cancelled </span>
                @else
                    <span class="status"> Validated </span>
                @endif
                @if (!$reservation['cancelled'])
                    {{-- Cancellation form --}}
                    <form class="reserve-form" action="{{ route('reserve.cancel') }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="form-group {!! $errors->has('equipment_is_not_reservable') ? 'haserror' : '' !!}">
                            {!! $errors->first('equipment_is_not_reservable', '<small class="helpblock">:message</small>') !!}
                        </div>
                        <div class="form-group {!! $errors->has('equipment_id') ? 'haserror' : '' !!}">
                            <input type="hidden" name="equipment_id" value="{{ $reservation['equipment_id'] }}">
                            {!! $errors->first('equipment_id', '<small class="helpblock">:message</small>') !!}
                        </div>
                        <div class="form-group {!! $errors->has('user_id') ? 'haserror' : '' !!}">
                            <input type="hidden" name="user_id" value="{{ $reservation['user_id'] }}">
                            {!! $errors->first('user_id', '<small class="helpblock">:message</small>') !!}
                        </div>
                        <input type="hidden" name="id" value="{{ $reservation['id'] }}">
                        <input type="submit" class="cancel-reserve-button" value="cancel reservation">
                    </form>
                @endif
            </div>
        @endforeach
    </div>
@endsection
