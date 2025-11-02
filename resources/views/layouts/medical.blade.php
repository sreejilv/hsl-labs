<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HSL Labs') }} - Medical Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #3498db 0%, #2980b9 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            background-color: #e74c3c;
            color: #fff;
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
        }
        .main-content {
            transition: all 0.3s;
        }
        .top-navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-bottom: 1px solid #e9ecef;
        }
        .user-avatar {
            width: 35px;
            height: 35px;
            background: #e74c3c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .sidebar-toggle {
            display: none;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -250px;
                z-index: 1000;
                width: 250px;
            }
            .sidebar.show {
                left: 0;
            }
            .sidebar-toggle {
                display: block;
            }
            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 sidebar px-0" id="sidebar">
                <div class="p-3">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center">
                            <div class="user-avatar me-2">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <h5 class="text-white mb-0">Medical Portal</h5>
                        </div>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('medical.dashboard') ? 'active' : '' }}" href="{{ route('medical.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('medical.patients.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="collapse" 
                               data-bs-target="#patientRecordsMenu" aria-expanded="{{ request()->routeIs('medical.patients.*') ? 'true' : 'false' }}">
                                <i class="fas fa-user-injured"></i>Patient Records
                            </a>
                            <div class="collapse {{ request()->routeIs('medical.patients.*') ? 'show' : '' }}" id="patientRecordsMenu">
                                <ul class="nav nav-pills flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('medical.patients.create') ? 'active' : '' }}" href="{{ route('medical.patients.create') }}">
                                            <i class="fas fa-user-plus"></i>Add Patient
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('medical.patients.index') ? 'active' : '' }}" href="{{ route('medical.patients.index') }}">
                                            <i class="fas fa-list"></i>Patient List
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('medical.patients.trashed') ? 'active' : '' }}" href="{{ route('medical.patients.trashed') }}">
                                            <i class="fas fa-archive"></i>Archived Patients
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-calendar-alt"></i>Appointments
                            </a>
                        </li>
                        @if(auth()->user()->hasRole('surgeon'))
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('medical.staff.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="collapse" 
                               data-bs-target="#staffManagementMenu" aria-expanded="{{ request()->routeIs('medical.staff.*') ? 'true' : 'false' }}">
                                <i class="fas fa-users"></i>Staff Management
                            </a>
                            <div class="collapse {{ request()->routeIs('medical.staff.*') ? 'show' : '' }}" id="staffManagementMenu">
                                <ul class="nav nav-pills flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('medical.staff.create') ? 'active' : '' }}" href="{{ route('medical.staff.create') }}">
                                            <i class="fas fa-user-plus"></i>Add Staff
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('medical.staff.index') ? 'active' : '' }}" href="{{ route('medical.staff.index') }}">
                                            <i class="fas fa-list"></i>Staff List
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-pills"></i>Pharmacy
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-line"></i>Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cog"></i>Settings
                            </a>
                        </li>
                        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-start w-100 border-0 p-0" 
                                        style="color: #ecf0f1; padding: 0.75rem 1rem !important;">
                                    <i class="fas fa-sign-out-alt"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 ms-sm-auto main-content">
                <!-- Top Navigation Bar -->
                <nav class="navbar navbar-expand-lg top-navbar px-3 py-2">
                    <button class="btn btn-outline-secondary sidebar-toggle me-3" type="button" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" 
                               data-bs-toggle="dropdown">
                                <div class="user-avatar me-2">
                                    {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                                </div>
                                <div class="d-none d-md-block">
                                    <div class="fw-bold">{{ auth()->user()->first_name ?? auth()->user()->name }} {{ auth()->user()->last_name ?? '' }}</div>
                                    <small class="text-muted">
                                        @if(auth()->user()->hasRole('surgeon'))
                                            Surgeon
                                        @elseif(auth()->user()->hasRole('staff'))
                                            Staff Member
                                        @else
                                            User
                                        @endif
                                    </small>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li class="dropdown-header">
                                    <strong>{{ auth()->user()->first_name ?? auth()->user()->name }} {{ auth()->user()->last_name ?? '' }}</strong>
                                    <br><small class="text-muted">{{ auth()->user()->email }}</small>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a></li>
                                <li><a class="dropdown-item" href="#">
                                    <i class="fas fa-cog me-2"></i>Account Settings
                                </a></li>
                                @if(auth()->user()->hasRole('surgeon'))
                                <li><a class="dropdown-item" href="{{ route('medical.staff.index') }}">
                                    <i class="fas fa-users me-2"></i>Manage Staff
                                </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="p-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
</body>
</html>