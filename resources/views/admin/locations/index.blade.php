@extends('admin.layouts.app')

@section('content')
<h1>Location Management</h1>

<div class="card">
    <h3>Add Block</h3>
    <form method="POST" action="{{ route('admin.blocks.store') }}">
        @csrf
        <input type="text" name="block_name" placeholder="Block Name">
        <button>Add Block</button>
    </form>
</div>

<div class="card">
    <h3>Add Room</h3>
    <form method="POST" action="{{ route('admin.rooms.store') }}">
        @csrf

        <select name="block_id">
            @foreach($allBlocks as $block)
                <option value="{{ $block->id }}">
                    {{ $block->block_name }}
                </option>
            @endforeach
        </select>

        <input type="number" name="floor_number" placeholder="Floor Number">
        <input type="text" name="room_name" placeholder="Room Name">

        <button>Add Room</button>
    </form>
</div>

<div class="card">
    <h3>All Locations</h3>

    @foreach($blocks as $block)
        <strong>{{ $block->block_name }}</strong>

        @php
            $grouped = $block->rooms->groupBy('floor_number');
        @endphp

        @foreach($grouped as $floor => $rooms)
            <p><em>Floor {{ $floor }}</em></p>
            <ul>
                @foreach($rooms as $room)
                    <li>
                        {{ $room->room_name }}

                        <!-- ✏️ Edit -->
                        <a href="{{ route('admin.rooms.edit', $room->id) }}"
                           style="margin-left:10px;color:#2563eb;">
                            Edit
                        </a>

                        <!-- 🗑️ Delete -->
                        <form method="POST"
                              action="{{ route('admin.rooms.delete', $room->id) }}"
                              style="display:inline"
                              onsubmit="return confirm('Delete this room?')">
                            @csrf
                            @method('DELETE')
                            <button
                                style="background:#dc2626;color:white;
                                       border:none;padding:4px 8px;
                                       border-radius:4px;margin-left:5px;">
                                Delete
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endforeach
    @endforeach
</div>
@endsection
