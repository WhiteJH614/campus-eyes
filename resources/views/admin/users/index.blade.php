@extends('admin.layouts.app')

@section('content')
<h1>Student Users</h1>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th> <!-- NEW -->
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <form method="POST"
                              action="{{ route('admin.students.delete', $user->id) }}"
                              onsubmit="return confirm('Delete this student?')">
                            @csrf
                            @method('DELETE')
                            <button
                                style="background:#dc2626;
                                       color:white;
                                       border:none;
                                       padding:6px 10px;
                                       border-radius:6px;
                                       cursor:pointer;">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
