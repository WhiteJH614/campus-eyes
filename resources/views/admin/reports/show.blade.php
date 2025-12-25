@extends('admin.layouts.app')

@section('content')
<h1>Report #{{ $report->id }}</h1>

<div class="card">
    <p><strong>Description:</strong> {{ $report->description }}</p>
    <p><strong>Urgency:</strong> {{ $report->urgency }}</p>
    <p><strong>Status:</strong> {{ $report->status }}</p>
    <p><strong>Location:</strong>
        {{ $report->room->block->block_name }} - {{ $report->room->room_name }}
    </p>
</div>

<div class="card">
    <h3>Technician Assignment</h3>

    @if ($report->technician)
        <p><strong>Assigned Technician:</strong> {{ $report->technician->name }}</p>
    @else
        <form method="POST" action="{{ route('admin.reports.assign', $report) }}">
            @csrf
            @method('PUT')

            <select name="technician_id" required>
                @foreach ($technicians as $tech)
                    <option value="{{ $tech->id }}">
                        {{ $tech->name }} ({{ $tech->availability_status }})
                    </option>
                @endforeach
            </select>

            <br><br>

            <button type="submit">Assign Technician</button>
        </form>
    @endif
</div>
@endsection
