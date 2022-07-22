@extends('template')
@section('contenu')
    <h2>List of borrows</h2>
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
                <label for="status-filter-all">Currently running</label>
                <input class="status-filter-radio" type="radio" value="currently_running" name="status-filter">
            </div>
        </form>
    </div>
    <div class="panel panel-info">
        {{-- Borrows list --}}
        @foreach ($data['borrows'] as $borrow)
            <div class="borrow" data-currentlyRunning="{{ $borrow['currently_running'] }}"
                $data-userId="{{ $borrow['user_id'] }}" $data-equipmentId="{{ $borrow['equipment_id'] }}"
                $data-start="{{ $borrow['start'] }}" $data-end="{{ $borrow['end'] }}">
                <a href="/equipments#equipment-{{ $borrow['equipment_id'] }}">
                    <img style="max-width: 50px" src="{{ asset('/storage/images/' . $borrow['equ_img_url']) }}">
                </a>
                <span class="name"> {{ $borrow['equ_name'] }} </span>
                <span class="start"> From {{ \Carbon\Carbon::parse($borrow['start'])->format('l j F Y') }}
                    to {{ \Carbon\Carbon::parse($borrow['end'])->format('l j F Y') }}
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
            </div>
        @endforeach
    </div>
@endsection

<script> 
console.log("coucou");   
</script>
