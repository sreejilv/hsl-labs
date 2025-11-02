<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HSL Labs') }} - Admin Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
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
            background-color: #3498db;
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
            background: #3498db;
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
                            <div class="user-avatar me-2">HSL</div>
                            <h5 class="text-white mb-0">Admin Panel</h5>
                        </div>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.surgeon.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="collapse" 
                               data-bs-target="#userManagementMenu" aria-expanded="{{ request()->routeIs('admin.surgeon.*') ? 'true' : 'false' }}">
                                <i class="fas fa-users"></i>User Management
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.surgeon.*') ? 'show' : '' }}" id="userManagementMenu">
                                <ul class="nav nav-pills flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.surgeon.register') ? 'active' : '' }}" href="{{ route('admin.surgeon.register') }}">
                                            <i class="fas fa-user-plus"></i>Register Doctor
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.surgeon.list') ? 'active' : '' }}" href="{{ route('admin.surgeon.list') }}">
                                            <i class="fas fa-list"></i>Doctor List
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                                <i class="fas fa-box"></i>Product Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.purchase-orders.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="collapse" 
                               data-bs-target="#purchaseOrdersAdminMenu" aria-expanded="{{ request()->routeIs('admin.purchase-orders.*') ? 'true' : 'false' }}">
                                <i class="fas fa-shopping-cart"></i>Orders
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.purchase-orders.*') ? 'show' : '' }}" id="purchaseOrdersAdminMenu">
                                <ul class="nav nav-pills flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.purchase-orders.index') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.index') }}">
                                            <i class="fas fa-list"></i>All Orders
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.purchase-orders.history') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.history') }}">
                                            <i class="fas fa-history"></i>Order History
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.purchase-orders.inventory') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.inventory') }}">
                                            <i class="fas fa-warehouse"></i>Inventory Status
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                                <i class="fas fa-cog"></i>System Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.account.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="collapse" 
                               data-bs-target="#accountMenu" aria-expanded="{{ request()->routeIs('admin.account.*') ? 'true' : 'false' }}">
                                <i class="fas fa-wallet"></i>Financial Account
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.account.*') ? 'show' : '' }}" id="accountMenu">
                                <ul class="nav nav-pills flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.account.wallet') ? 'active' : '' }}" href="{{ route('admin.account.wallet') }}">
                                            <i class="fas fa-credit-card"></i>Wallet & Balance
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.account.transactions') ? 'active' : '' }}" href="{{ route('admin.account.transactions') }}">
                                            <i class="fas fa-exchange-alt"></i>Transactions
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.account.orders') ? 'active' : '' }}" href="{{ route('admin.account.orders') }}">
                                            <i class="fas fa-shopping-cart"></i>Orders
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.account.reports') ? 'active' : '' }}" href="{{ route('admin.account.reports') }}">
                                            <i class="fas fa-chart-bar"></i>Financial Reports
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-bar"></i>Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.promotion.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="collapse" 
                               data-bs-target="#promotionMenu" aria-expanded="{{ request()->routeIs('admin.promotion.*') ? 'true' : 'false' }}">
                                <i class="fas fa-gift"></i>Promotion
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.promotion.*') ? 'show' : '' }}" id="promotionMenu">
                                <ul class="nav nav-pills flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.promotion.rewards') ? 'active' : '' }}" href="#">
                                            <i class="fas fa-trophy"></i>Rewards
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.promotion.coupons') ? 'active' : '' }}" href="#">
                                            <i class="fas fa-ticket-alt"></i>Coupons
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-database"></i>Backup & Restore
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
                    
                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" 
                               id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="user-avatar me-2">{{ substr(Auth::user()->name, 0, 1) }}</div>
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text">
                                    <i class="fas fa-user me-2"></i>Profile
                                </span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="container-fluid px-4 py-3">
                    @yield('content')
                    {{ $slot ?? '' }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    
    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        });
    </script>
</body>
</html>