<!-- Author: Ivan Goh Shern Rune -->

@extends('admin.layouts.app')

@section('content')
<h1>Edit Room</h1>

<form method="POST" action="{{ route('admin.rooms.update', $room->id) }}">
    @csrf
    @method('PUT')

    <label>Block</label>
    <select name="block_id">
        @foreach($blocks as $block)
            <option value="{{ $block->id }}"
                {{ $room->block_id == $block->id ? 'selected' : '' }}>
                {{ $block->block_name }}
            </option>
        @endforeach
    </select>

    <label>Floor Number</label>
    <input type="number" name="floor_number" value="{{ $room->floor_number }}">

    <label>Room Name</label>
    <input type="text" name="room_name" value="{{ $room->room_name }}">

    <button>Update Room</button>
</form>
@endsection
