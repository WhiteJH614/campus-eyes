<!-- Author: Ivan Goh Shern Rune -->

@extends('admin.layouts.app')

@section('content')
<h1>Admin Dashboard</h1>
<style>
    .stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #ffffff;
    padding: 25px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.stat-card h3 {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
}

.stat-card p {
    font-size: 32px;
    font-weight: bold;
    color: #2563eb;
}

/* Status colors */
.stat-card.warning p {
    color: #d97706;
}

.stat-card.success p {
    color: #16a34a;
}
    </style>
<!-- STAT CARDS -->
<div class="stats">
    <div class="stat-card">
        <h3>Total Tickets</h3>
        <p>{{ $totalReports }}</p>
    </div>

    <div class="stat-card warning">
        <h3>Unassigned Tickets</h3>
        <p>{{ $unassignedReports }}</p>
    </div>

    <div class="stat-card success">
        <h3>Completed Tickets</h3>
        <p>{{ $completedReports }}</p>
    </div>
</div>

<!-- FAULTS BY BUILDING -->
<div class="card">
    <h3>Faults by Building</h3>

    <table>
        <thead>
            <tr>
                <th>Building</th>
                <th>Total Faults</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($faultsByBuilding as $row)
                <tr>
                    <td>{{ $row->block_name }}</td>
                    <td>{{ $row->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
