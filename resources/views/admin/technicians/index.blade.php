<!-- Author: Ivan Goh Shern Rune -->

@extends('admin.layouts.app')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Technicians</h1>
        <a href="{{ route('admin.add_technician') }}"
            style="background:#3498DB; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;">
            Add Technician
        </a>
    </div>

    <form method="GET" action="{{ route('admin.technicians.index') }}">
        <input type="text" name="search" placeholder="Search name or email" value="{{ request('search') }}">
        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>

        @foreach($technicians as $tech)
            <tr>
                <td>{{ $tech->name }}</td>
                <td>{{ $tech->email }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.technicians.destroy', $tech->id) }}"
                        onsubmit="return confirm('Remove this technician?')">
                        @csrf
                        @method('DELETE')
                        <button style="background:#dc2626;color:white">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection