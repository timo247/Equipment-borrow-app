@extends('template')
@section('contenu')
<form action="reserve" method="post">
@csrf
{{ $id = 7;}}
<input type="text" name="equipment_id" value='{{$id}}'>
<label>start</label>
<input type="date" name="start">
<label>start</label>
<input type="date" name="end"> 
<input type="submit" value="submit">  
</form>
@endsection