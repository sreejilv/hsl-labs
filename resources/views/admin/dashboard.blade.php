@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                </h1>
                <p class="text-muted">
                    Welcome back, {{ Auth::user()->name }}! 
                    <span class="badge bg-danger ms-2">Administrator</span>
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $totalUsers }}</h4>
                                    <p class="mb-0">Total Users</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $doctorStats['active_doctors'] }}</h4>
                                    <p class="mb-0">Active Doctors</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-md fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $doctorStats['total_doctors'] }}</h4>
                                    <p class="mb-0">Total Doctors</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-stethoscope fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $doctorStats['inactive_doctors'] }}</h4>
                                    <p class="mb-0">Inactive Doctors</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-times fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Panels -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-users me-2"></i>User Management
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.surgeon.register') }}" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>Register New Doctor
                                </a>
                                <a href="{{ route('admin.surgeon.list') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-2"></i>View Doctor List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-cog me-2"></i>System Management
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.settings') }}" class="btn btn-secondary">
                                    <i class="fas fa-cog me-2"></i>System Settings
                                </a>
                                <a href="#" class="btn btn-outline-secondary">
                                    <i class="fas fa-database me-2"></i>Backup & Restore
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bell me-2"></i>Recent Activity
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-user-plus text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">New doctor registered</div>
                                            <small class="text-muted">2 minutes ago</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-database text-warning"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">Database backup completed</div>
                                            <small class="text-muted">2 hours ago</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
