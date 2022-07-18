@extends('template')
@section('contenu')
    @if (Auth::check())
        <h2>List of equipments</h2>
        <div class="panel panel-info">
            @foreach ($data['equipments'] as $equipment)
                <div class="equipment" data-available="{{ $equipment['availability'] }}">
                    <img style="max-width: 200px" src="{{ asset('/storage/images/' . $equipment['image_url']) }}">
                    <span class="name"> {{ $equipment['name'] }} </span>
                    <span class="description"> {{ $equipment['description'] }}</span>
                    <span class="availability">{{ $equipment['availability'] }}</span>
                    @if (!empty($equipment['reservations']))
                        @foreach ($equipment['reservations'] as $reservation)
                            <div class="reservation" style="background-color: blueviolet">
                                <span class="reservation">
                                    Reserved from: {{ \Carbon\Carbon::parse($reservation['start'])->format('l j F') }}
                                    to {{ \Carbon\Carbon::parse($reservation['end'])->format('l j F') }}
                                </span>
                                @can('isAdmin')
                                    <span style="background-color:green">by {{ $reservation['username'] }}</span>
                                    @if ($reservation['start_validation'] == null)
                                        <form class="reserve-form" action="{{ route('reserve.accept') }}" method="POST">
                                            @csrf
                                            @method('POST')
                                            <input type="submit" class="accept-reserve-button" value="accept reservation">
                                        </form>
                                    @endif
                                    <form class="reserve-form" action="{{ route('reserve.cancel') }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <input type="submit" class="cancel-reserve-button" value="cancel reservation">
                                    </form>
                                @endcan
                            </div>
                        @endforeach
                    @endif
                    @if ($equipment['borrow'] != null)
                        @can('isAdmin')
                            <span class="borrow"> Borrowed since:
                                {{ \Carbon\Carbon::parse($equipment['borrow']['start'])->format('l j F') }}</span>
                            <span style="background-color:red">by {{ $equipment['borrow']['username'] }}</span>
                        @endcan
                    @endif
                    <form class="reserve-form" action="{{ route('reservation.store') }} " method="POST"
                        acceptcharset="UTF-8" class="form-horizontalpanel">
                        @csrf
                        @method('POST')
                        @can('isAdmin')
                            <select type="select" name="user_id">
                                @foreach ($data['users'] as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['username'] }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" readonly>
                        @endcan
                        <input style="display_none" type="hidden" name="equipment_id" value="{{ $equipment['id'] }}">
                        <div class="form-group {!! $errors->has('from') ? 'haserror' : '' !!}">
                            <label for="from">from</label>
                            <input class="form-control" type="date" name="from" value="{{ old('from') }}">
                            {!! $errors->first('from', '<small class="helpblock">:message</small>') !!}
                        </div>
                        <div class="form-group {!! $errors->has('to') ? 'haserror' : '' !!}">
                            <label for="to">to</label>
                            <input class="form-control" type="date" name="to" value="{{ old('to') }}">
                            {!! $errors->first('to', '<small class="helpblock">:message</small>') !!}
                        </div>
                        <input type="submit" class="reserve-button" value="reserve">
                    </form>
                    @can('isAdmin')
                        @if ($equipment['borrow'] == null)
                            <form class="borrow-form" action="{{ route('borrow.start') }}" method="POST">
                                @csrf
                                @method('POST')
                                <select type="select" name="user-id">
                                    @foreach ($data['users'] as $user)
                                        <option value="{{ $user['id'] }}">{{ $user['username'] }}</option>
                                    @endforeach
                                </select>
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
    @endif
@endsection
