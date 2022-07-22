@extends('template')
@section('contenu')
    <h2>List of reservations</h2>
    {{-- fitler for user to chose what to display --}}
    <div class="filter">
        <form class="filter-form" action="/" method="GET">
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
                <input class="status-filter-radio" type="radio" value="validated" name="status-filter">
                <label for="status-filter-cancelled">Cancelled</label>
                <input class="status-filter-radio" type="radio" value="cancelled" name="status-filter">
            </div>
            <input class="submit-filter" type="submit" value="apply filters">
        </form>
    </div>
    <div class="panel panel-info">
        {{-- Equipments list --}}
        @foreach ($data['reservations'] as $reservation)
            <div class="reservation" data-currentlyRunning="{{ $reservation['currently_running'] }}"
                data-awaintingValidation="{{ $reservation['awaiting_validation'] }}"
                data-cancelled="{{ $reservation['cancelled'] }}"
                data-userId="{{ $reservation['user_id'] }}"
                data-equipmentId="{{ $reservation['equipment_id'] }}"
                data-start="{{ \Carbon\Carbon::parse($reservation['start'])->format('l j F Y') }}"
                data-end="{{ \Carbon\Carbon::parse($reservation['end'])->format('l j F Y')  }}"
                data-fromOk = true
                data-toOk = true>
                <a href="/equipments#equipment-{{ $reservation["equipment_id"] }}">
                    <img style="max-width: 50px" src="{{ asset('/storage/images/' . $reservation['equ_img_url']) }}">
                </a>
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


    <script>
        const   filter_form = document.querySelector('.filter-form')
        function filterByFrom(reservations){
            const from_input = document.querySelector('.from-input')
            from_input.addEventListener('input', (e) => {
                reservations.forEach(reservation => {
                   const start_form = new Date(e.target.value)
                   const start_reservation = new Date(reservation.dataset.start)
                    if(start_reservation > start_form){
                        reservation.dataset.fromOk = true
                    } else {
                        reservation.dataset.fromOk = false
                    }
                });
            })
        }  

        function filterByTo(reservations){
            const to_input = document.querySelector('.to-input')
            to_input.addEventListener('input', (e) => {
                reservations.forEach(reservation => {
                   const end_form = new Date(e.target.value)
                   const end_reservation = new Date(reservation.dataset.end)
                    if(end_reservation < end_form){
                        reservation.dataset.toOk = true 
                    } else {
                        reservation.dataset.toOk = false
                    }
                });
            })
        }

        function hideAndShowReservations(reservations){
            console.log("on hide", reservations)
            reservations.forEach(reservation => {
                if(!reservation.dataset.fromOk || !reservation.dataset.toOk){
                    reservation.classList.add('hidden')
                } else {
                    reservation.classList.remove('hidden')
                }
            })
        }

        const reservations = document.querySelectorAll('.reservation')  
        filterByFrom(reservations)
        filterByTo(reservations)

        filter_form.addEventListener('submit', (e) => {
            console.log('submit')
            e.preventDefault();
            const reservations = document.querySelectorAll('.reservation')  
            hideAndShowReservations(reservations)
        })
        
        

    </script>
@endsection


