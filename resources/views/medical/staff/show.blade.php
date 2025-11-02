@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-user me-2"></i>Staff Details</h1>
                <div>
                    <a href="{{ route('medical.staff.edit', $staff) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit Staff
                    </a>
                    <a href="{{ route('medical.staff.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Staff List
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <!-- Personal Information Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Full Name</label>
                                        <p class="fs-5 fw-bold">{{ $staff->full_name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Email Address</label>
                                        <p><a href="mailto:{{ $staff->user->email }}" class="text-decoration-none">{{ $staff->user->email }}</a></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Phone Number</label>
                                        <p>
                                            @if($staff->user->phone)
                                                <a href="tel:{{ $staff->user->phone }}" class="text-decoration-none">{{ $staff->user->phone }}</a>
                                            @else
                                                <span class="text-muted">Not provided</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Date of Birth</label>
                                        <p>
                                            @if($staff->user->date_of_birth)
                                                {{ $staff->user->date_of_birth->format('F j, Y') }}
                                                <small class="text-muted">({{ $staff->user->date_of_birth->age }} years old)</small>
                                            @else
                                                <span class="text-muted">Not provided</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Gender</label>
                                        <p>{{ $staff->user->gender ? ucfirst($staff->user->gender) : 'Not specified' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Account Created</label>
                                        <p>{{ $staff->user->created_at->format('F j, Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($staff->user->address)
                            <div class="mb-3">
                                <label class="form-label text-muted">Address</label>
                                <p>{{ $staff->user->address }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Work Information Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Work Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Staff ID</label>
                                        <p><code class="fs-6">{{ $staff->staff_id }}</code></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Position</label>
                                        <p class="fw-bold">{{ $staff->position ?: 'Not specified' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Work Shift</label>
                                        <p>
                                            @if($staff->shift === 'day')
                                                <span class="badge bg-warning fs-6"><i class="fas fa-sun me-1"></i>Day Shift</span>
                                            @elseif($staff->shift === 'night')
                                                <span class="badge bg-dark fs-6"><i class="fas fa-moon me-1"></i>Night Shift</span>
                                            @elseif($staff->shift === 'rotating')
                                                <span class="badge bg-info fs-6"><i class="fas fa-sync me-1"></i>Rotating Shift</span>
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Hire Date</label>
                                        <p>
                                            {{ $staff->hire_date_formatted }}
                                            @if($staff->hire_date)
                                                <br><small class="text-muted">{{ $staff->tenure }} of service</small>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Salary</label>
                                        <p>
                                            @if($staff->salary)
                                                <strong>${{ number_format($staff->salary, 2) }}</strong>
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Employment Status</label>
                                <p>
                                    @if($staff->is_active)
                                        <span class="badge bg-success fs-6"><i class="fas fa-check-circle me-1"></i>Active</span>
                                    @else
                                        <span class="badge bg-secondary fs-6"><i class="fas fa-ban me-1"></i>Inactive</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact Card -->
                    @if($staff->emergency_contact_name || $staff->emergency_contact_phone)
                    <div class="card shadow mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Emergency Contact</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Contact Name</label>
                                        <p>{{ $staff->emergency_contact_name ?: 'Not provided' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Contact Phone</label>
                                        <p>
                                            @if($staff->emergency_contact_phone)
                                                <a href="tel:{{ $staff->emergency_contact_phone }}" class="text-decoration-none">{{ $staff->emergency_contact_phone }}</a>
                                            @else
                                                Not provided
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Additional Information Card -->
                    @if($staff->qualifications || $staff->notes)
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Additional Information</h5>
                        </div>
                        <div class="card-body">
                            @if($staff->qualifications)
                            <div class="mb-3">
                                <label class="form-label text-muted">Qualifications & Certifications</label>
                                <div class="border-start border-3 border-info ps-3">
                                    <p class="mb-0">{{ $staff->qualifications }}</p>
                                </div>
                            </div>
                            @endif

                            @if($staff->notes)
                            <div class="mb-3">
                                <label class="form-label text-muted">Notes</label>
                                <div class="border-start border-3 border-warning ps-3">
                                    <p class="mb-0">{{ $staff->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Action Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Actions Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="card-body d-grid gap-2">
                            <a href="{{ route('medical.staff.edit', $staff) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Edit Staff Details
                            </a>
                            
                            <form method="POST" action="{{ route('medical.staff.toggle-status', $staff) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-{{ $staff->is_active ? 'warning' : 'success' }} w-100">
                                    <i class="fas fa-{{ $staff->is_active ? 'ban' : 'check' }} me-2"></i>
                                    {{ $staff->is_active ? 'Deactivate' : 'Activate' }} Staff
                                </button>
                            </form>

                            <hr>

                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-2"></i>Delete Staff
                            </button>
                        </div>
                    </div>

                    <!-- Statistics Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Staff Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary">{{ $staff->tenure_days }}</h4>
                                        <small class="text-muted">Days Employed</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success">{{ $staff->is_active ? 'Active' : 'Inactive' }}</h4>
                                    <small class="text-muted">Current Status</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info Card -->
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-address-card me-2"></i>Contact Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-envelope text-primary me-3"></i>
                                <div>
                                    <small class="text-muted">Email</small>
                                    <br><a href="mailto:{{ $staff->user->email }}" class="text-decoration-none">{{ $staff->user->email }}</a>
                                </div>
                            </div>

                            @if($staff->user->phone)
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-phone text-success me-3"></i>
                                <div>
                                    <small class="text-muted">Phone</small>
                                    <br><a href="tel:{{ $staff->user->phone }}" class="text-decoration-none">{{ $staff->user->phone }}</a>
                                </div>
                            </div>
                            @endif

                            @if($staff->emergency_contact_phone)
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-danger me-3"></i>
                                <div>
                                    <small class="text-muted">Emergency</small>
                                    <br><a href="tel:{{ $staff->emergency_contact_phone }}" class="text-decoration-none">{{ $staff->emergency_contact_phone }}</a>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Staff Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                </div>
                <p class="text-center">Are you sure you want to delete <strong>{{ $staff->full_name }}</strong>?</p>
                <div class="alert alert-danger">
                    <strong>Warning:</strong> This action will:
                    <ul class="mb-0 mt-2">
                        <li>Permanently delete the staff member's account</li>
                        <li>Remove all associated staff details</li>
                        <li>This action cannot be undone</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('medical.staff.destroy', $staff) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Staff
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection