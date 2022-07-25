@extends('template')
@section('contenu')
    <h2>List of reservations</h2>
    {{-- fitler for user to chose what to display --}}
    <div class="filter">
        <form class="filter-form" action="/" method="GET">
            {{-- filter by equipment id --}}
            <label for="equipment">Equipment</label>
            <select class="select-equipment-id">
                <option value="all"> All equipments </option>
                @foreach ($data['equipments'] as $eq)
                    <option value="{{ $eq['id'] }}">{{ $eq['name'] }}</option>
                @endforeach
            </select>
            {{-- filter by user id --}}
            <label for="user">User</label>
            <select class="select-user-id">
                <option value="all"> All users </option>
                @foreach ($data['users'] as $user)
                    <option value="{{ $user['id'] }}">{{ $user['username'] }}</option>
                @endforeach
            </select>
            {{-- filter by date --}}
            <label for="from-label">From</label>
            <input class="from-input" type="date" value="2001-07-20">
            <label for="to-label">To</label>
            <input class="to-input" type="date" value="2031-07-20">
            {{-- filter by reservation status --}}
            <div class="status-filter">
                <label>Show only equipments with status:</label>
                <label for="status-filter-all">All</label>
                <input class="status-filter-radio" type="radio" value="all" name="status-filter">
                <label for="status-filter-all">Unvalidated</label>
                <input class="status-filter-radio" type="radio" value="unvalidated" name="status-filter">
                <label for="status-filter-currentlyRunning">Currently runnning</label>
                <input class="status-filter-radio" type="radio" value="currently_running" name="status-filter">
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
                data-awaiting-validation="{{ $reservation['awaiting_validation'] }}"
                data-cancelled="{{ $reservation['cancelled'] }}" data-user-id="{{ $reservation['user_id'] }}"
                data-equipment-id="{{ $reservation['equipment_id'] }}"
                data-start="{{ \Carbon\Carbon::parse($reservation['start'])->format('l j F Y') }}"
                data-end="{{ \Carbon\Carbon::parse($reservation['end'])->format('l j F Y') }}" data-from-ok=true
                data-to-ok=true data-currently-running-ok=true data-cancelled-ok=true data-unvalidated-ok=true
                data-equipment-filter-ok=true data-user-filter-ok = true>
                <a href="/equipments#equipment-{{ $reservation['equipment_id'] }}">
                    <img style="max-width: 50px" src="{{ asset('/storage/images/' . $reservation['equ_img_url']) }}">
                </a>
                <span class="name"> {{ $reservation['equ_name'] }} </span>
                <span class="start"> From {{ \Carbon\Carbon::parse($reservation['start'])->format('l j F Y') }}
                    to {{ \Carbon\Carbon::parse($reservation['end'])->format('l j F Y') }}
                </span>
                @can('isAdmin')
                    <span style="background-color:green">by {{ $reservation['username'] }}</span>
                @endcan
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
        function filterByFrom(reservations) {
            const from_input = document.querySelector('.from-input')
            from_input.addEventListener('input', (e) => {
                reservations.forEach(reservation => {
                    console.log("from input val", e.target.value);
                    const start_form = new Date(e.target.value)
                    const start_reservation = new Date(reservation.dataset.start)
                    if (start_reservation > start_form) {
                        reservation.dataset.fromOk = true
                    } else {
                        reservation.dataset.fromOk = false
                    }
                });
            })
        }

        function filterByTo(reservations) {
            const to_input = document.querySelector('.to-input')
            to_input.addEventListener('input', (e) => {
                reservations.forEach(reservation => {
                    const end_form = new Date(e.target.value)
                    const end_reservation = new Date(reservation.dataset.end)
                    if (end_reservation < end_form) {
                        reservation.dataset.toOk = true
                    } else {
                        reservation.dataset.toOk = false
                    }
                });
            })
        }

        function filterByStatus(reservations) {
            const status_filter_radio_inputs = document.querySelectorAll('.status-filter-radio');
            // console.log(status_filter_radio_inputs);
            status_filter_radio_inputs.forEach(input => {
                input.addEventListener('input', (e) => {
                    reservations.forEach(reservation => {
                            reservation.dataset.currentlyRunningOk = true;
                            reservation.dataset.unvalidatedOk = true;
                            reservation.dataset.cancelledOk = true;
                        if (e.target.value == "cancelled"){
                            if(!reservation.dataset.cancelled){ reservation.dataset.cancelledOk = false }
                        } else if (e.target.value == "unvalidated"){
                            if(!reservation.dataset.awaitingValidation){ reservation.dataset.unvalidatedOk = false }
                        } else if (e.target.value == "currently_running"){
                            if(!reservation.dataset.currentlyRunning){reservation.dataset.currentlyRunningOk = false}
                        }
                    });
                })
            })
        }

        function filterByEquipment(interactions) {
            const select_equipment_id = document.querySelector('.select-equipment-id');
            select_equipment_id.addEventListener('change', (e) => {
                interactions.forEach(interaction => {
                    interaction.dataset.equipmentFilterOk = true
                    if (e.target.value != "all") {
                        if (interaction.dataset.equipmentId != e.target.value) {
                            interaction.dataset.equipmentFilterOk = false
                        }
                    }
                })
            })
        }

        function filterByUser(interactions) {
            const select_user_id = document.querySelector('.select-user-id');
            select_user_id.addEventListener('change', (e) => {
                console.log("user", e.target.value)
                interactions.forEach(interaction => {
                    interaction.dataset.userFilterOk = true
                    if (e.target.value != "all") {
                        if (interaction.dataset.userId != e.target.value) {
                            interaction.dataset.userFilterOk = false
                        }
                    }
                })
            })
        }

        function hideAndShowReservations(reservations) {
            reservations.forEach(reservation => {
                if (reservation.dataset.fromOk == "false" || reservation.dataset.toOk == "false" || reservation.dataset.unvalidatedOk == "false"
                || reservation.dataset.cancelledOk == "false" || reservation.dataset.currentlyRunningOk == "false" 
                || reservation.dataset.equipmentFilterOk == "false" || reservation.dataset.userFilterOk == "false") {
                    reservation.classList.add('hidden')
                } else {
                    reservation.classList.remove('hidden')
                }
            })
        }

        const filter_form = document.querySelector('.filter-form');
        const reservations = document.querySelectorAll('.reservation')
        filterByFrom(reservations)
        filterByTo(reservations)
        filterByStatus(reservations);
        filterByEquipment(reservations);
        filterByUser(reservations)
        filter_form.addEventListener('submit', (e) => {
            e.preventDefault();
            hideAndShowReservations(reservations)
        })
    </script>
@endsection
