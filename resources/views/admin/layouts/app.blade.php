<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Eyes | Admin</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f1f3f6;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background: #1f2937;
            color: #fff;
            padding: 20px;
            display: flex;
            flex-direction: column; /* 🔑 important */
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            padding: 12px;
            color: #d1d5db;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .sidebar a:hover {
            background: #374151;
            color: #fff;
        }
        
        .sidebar a.active {
            background: #2563eb;
            color: #fff;
            font-weight: 600;
        }

        /* Logout */
        .logout-form {
            margin-top: auto; /* 🔑 push to bottom */
        }

        .logout-form button {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: none;
            background: #dc2626;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
        }

        .logout-form button:hover {
            background: #b91c1c;
        }

        /* Content */
        .content {
            flex: 1;
            padding: 30px;
        }

        h1 {
            margin-bottom: 20px;
        }

        /* Cards */
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background: #f9fafb;
            font-weight: 600;
        }

        tr:hover {
            background: #f3f4f6;
        }

        /* Buttons */
        button, .btn {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
        }

        button {
            background: #2563eb;
            color: #fff;
        }

        button:hover {
            background: #1d4ed8;
        }
        
        .admin-info {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #374151;
            color: #d1d5db;
        }

        .admin-info strong {
            color: #fff;
            display: block;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="sidebar">
        <h2>Campus Eyes</h2>
        <div class="admin-info">
            <strong>{{ auth()->user()->name }}</strong><br>
            <small>Administrator</small>
        </div>

        <a href="{{ route('admin.dashboard') }}"
            class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('admin.reports.index') }}"
            class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            Reports
        </a>

        <a href="{{ route('admin.users.index') }}"
            class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            Students
        </a>

        <a href="{{ route('admin.technicians.index') }}"
            class="{{ request()->routeIs('admin.technicians.*') ? 'active' : '' }}">
            Technicians
        </a>

        <a href="{{ route('admin.locations.index') }}"
            class="{{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
            Locations
        </a>

        <!-- ✅ LOGOUT -->
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>

    <div class="content">
        @yield('content')
    </div>
</div>

</body>
</html>
