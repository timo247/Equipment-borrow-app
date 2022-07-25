@extends('template')
@section('contenu')
    <h2>List of borrows</h2>
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
                <label for="status-filter-currentlyRunning">Currently runnning</label>
                <input class="status-filter-radio" type="radio" value="currently_running" name="status-filter">
            </div>
            <input class="submit-filter" type="submit" value="apply filters">
        </form>
    </div>
    <div class="panel panel-info">
        {{-- Borrows list --}}
        @foreach ($data['borrows'] as $borrow)
            <div class="borrow" data-currentlyRunning="{{ $borrow['currently_running'] }}"
                data-user-id="{{ $borrow['user_id'] }}" data-equipment-id="{{ $borrow['equipment_id'] }}"
                data-start="{{ $borrow['start'] }}" data-end="{{ $borrow['end'] }}" data-from-ok=true data-to-ok=true
                data-currently-running="{{ $borrow['currently_running'] }}" data-currently-running-ok=true
                data-equipment-filter-ok=true data-user-filter-ok = true>
                <a href="/equipments#equipment-{{ $borrow['equipment_id'] }}">
                    <img style="max-width: 50px" src="{{ asset('/storage/images/' . $borrow['equ_img_url']) }}">
                </a>
                <span class="name"> {{ $borrow['equ_name'] }} </span>
                @if (!$borrow['currently_running'])
                    <span class="start"> From {{ \Carbon\Carbon::parse($borrow['start'])->format('l j F Y') }}
                        to {{ \Carbon\Carbon::parse($borrow['end'])->format('l j F Y') }}
                    </span>
                @else
                    <span class="start"> Since {{ \Carbon\Carbon::parse($borrow['start'])->format('l j F Y') }}
                    </span>
                    {{-- End  borrow form --}}
                    @can('isAdmin')
                        <form class="cancel-borrow-form" action="{{ route('borrow.end') }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="form-group {!! $errors->has('equipment_is_not_borrowed') ? 'haserror' : '' !!}">
                                {!! $errors->first('equipment_is_not_borrowed', '<small class="helpblock">:message</small>') !!}
                            </div>
                            <input type="hidden" name="equipment_id" value=" {{ $borrow['equipment_id'] }}">
                            <input type="hidden" name="borrow_id" value="{{ $borrow['id'] }}">
                            <input type="submit" class="cancel-borrow-button" value="end borrow">
                        </form>
                    @endcan
                @endif
                <span style="background-color:green">by {{ $borrow['username'] }}</span>
            </div>
        @endforeach
    </div>

    <script>
        function filterByFrom(borrows) {
            const from_input = document.querySelector('.from-input')
            from_input.addEventListener('input', (e) => {
                borrows.forEach(borrow => {
                    console.log("from input val", e.target.value);
                    const start_form = new Date(e.target.value)
                    const start_borrow = new Date(borrow.dataset.start)
                    if (start_borrow > start_form) {
                        borrow.dataset.fromOk = true
                    } else {
                        borrow.dataset.fromOk = false
                    }
                });
            })
        }

        function filterByTo(borrows) {
            const to_input = document.querySelector('.to-input')
            to_input.addEventListener('input', (e) => {
                borrows.forEach(borrow => {
                    const end_form = new Date(e.target.value)
                    const end_borrow = new Date(borrow.dataset.end)
                    if (end_borrow < end_form) {
                        borrow.dataset.toOk = true
                    } else {
                        borrow.dataset.toOk = false
                    }
                });
            })
        }

        function filterByStatus(borrows) {
            const status_filter_radio_inputs = document.querySelectorAll('.status-filter-radio');
            status_filter_radio_inputs.forEach(input => {
                input.addEventListener('input', (e) => {
                    borrows.forEach(borrow => {
                        borrow.dataset.currentlyRunningOk = true;
                        if (e.target.value == "currently_running") {
                            if (!borrow.dataset.currentlyRunning) {
                                borrow.dataset.currentlyRunningOk = false
                            }
                        }
                    });
                })
            })
        }

        function hideAndShowBorrows(borrows) {
            borrows.forEach(borrow => {
                console.log("hide", borrow.dataset)
                if (borrow.dataset.fromOk == "false" || borrow.dataset.toOk == "false" ||
                    borrow.dataset.currentlyRunningOk == "false" || borrow.dataset.equipmentFilterOk == "false" || borrow.dataset.userFilterOk == "false"
                ) {
                    borrow.classList.add('hidden')
                } else {
                    borrow.classList.remove('hidden')
                }
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

        const filter_form = document.querySelector('.filter-form')
        const borrows = document.querySelectorAll('.borrow')
        filterByFrom(borrows)
        filterByTo(borrows)
        filterByStatus(borrows);
        filterByEquipment(borrows)
        filterByUser(borrows)

        filter_form.addEventListener('submit', (e) => {
            e.preventDefault();
            hideAndShowBorrows(borrows)
        })
    </script>
@endsection
