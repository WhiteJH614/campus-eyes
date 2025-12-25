@extends('admin.layouts.app')

@section('content')
<h1>Reports</h1>

<form method="GET" style="margin-bottom: 20px;">
    <input type="text" name="search" placeholder="Search description">

    <select name="status">
        <option value="">All Status</option>
        <option value="Pending">Pending</option>
        <option value="Assigned">Assigned</option>
        <option value="Completed">Completed</option>
    </select>

    <button type="submit">Filter</button>
</form>

<div class="card">
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Location</th>
            <th>Urgency</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reports as $report)
        <tr>
            <td>#{{ $report->id }}</td>
            <td>{{ $report->category->name }}</td>
            <td>{{ $report->room->block->block_name }} - {{ $report->room->room_name }}</td>
            <td>{{ $report->urgency }}</td>
            <td>{{ $report->status }}</td>
            <td>
                <a href="{{ route('admin.reports.show', $report) }}">View</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection
