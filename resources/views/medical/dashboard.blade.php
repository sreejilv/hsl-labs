@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>Medical Dashboard
                </h1>
                <p class="text-muted">
                    Welcome back, {{ $user->name }}! 
                    @if($isSurgeon)
                        <span class="badge bg-primary ms-2">Surgeon</span>
                    @elseif($isStaff)
                        <span class="badge bg-secondary ms-2">Staff</span>
                    @endif
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>24</h4>
                                    <p class="mb-0">Today's Patients</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-injured fa-2x"></i>
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
                                    <h4>12</h4>
                                    <p class="mb-0">Appointments</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($isSurgeon)
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>8</h4>
                                    <p class="mb-0">Staff Members</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>5</h4>
                                    <p class="mb-0">Pending Tasks</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-tasks fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>3</h4>
                                    <p class="mb-0">Urgent Cases</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-grid">
                                        <a href="#" class="btn btn-outline-primary btn-lg">
                                            <i class="fas fa-user-plus me-2"></i>
                                            @if($isSurgeon) Add New Patient @else View Patients @endif
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-grid">
                                        <a href="#" class="btn btn-outline-success btn-lg">
                                            <i class="fas fa-calendar-plus me-2"></i>
                                            @if($isSurgeon) Schedule Appointment @else View Schedule @endif
                                        </a>
                                    </div>
                                </div>
                                @if($isSurgeon)
                                <div class="col-md-6 mb-3">
                                    <div class="d-grid">
                                        <a href="{{ route('medical.staff.create') }}" class="btn btn-outline-info btn-lg">
                                            <i class="fas fa-user-tie me-2"></i>Add Staff Member
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-grid">
                                        <a href="{{ route('medical.staff.index') }}" class="btn btn-outline-secondary btn-lg">
                                            <i class="fas fa-list me-2"></i>View Staff List
                                        </a>
                                    </div>
                                </div>
                                @else
                                <div class="col-md-6 mb-3">
                                    <div class="d-grid">
                                        <a href="#" class="btn btn-outline-warning btn-lg">
                                            <i class="fas fa-tasks me-2"></i>View My Tasks
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-grid">
                                        <a href="#" class="btn btn-outline-dark btn-lg">
                                            <i class="fas fa-flask me-2"></i>Lab Results
                                        </a>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Activity</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content ms-3">
                                            <h6 class="mb-1">Patient Check-in</h6>
                                            <p class="text-muted small mb-0">John Doe checked in for appointment</p>
                                            <small class="text-muted">10 minutes ago</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content ms-3">
                                            <h6 class="mb-1">Lab Results</h6>
                                            <p class="text-muted small mb-0">Blood test results available</p>
                                            <small class="text-muted">25 minutes ago</small>
                                        </div>
                                    </div>
                                </div>
                                @if($isSurgeon)
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-marker bg-info"></div>
                                        <div class="timeline-content ms-3">
                                            <h6 class="mb-1">Staff Update</h6>
                                            <p class="text-muted small mb-0">New staff member added</p>
                                            <small class="text-muted">1 hour ago</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-marker {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-top: 5px;
}
</style>
@endsection