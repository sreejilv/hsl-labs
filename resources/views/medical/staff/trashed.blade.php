@extends('layouts.medical')

@section('title', 'Deleted Staff Members')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-trash-alt me-2"></i>Deleted Staff Members
                    </h1>
                    <p class="text-muted">Manage deleted staff members - restore or permanently delete</p>
                </div>
                <div>
                    <a href="{{ route('medical.staff.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Active Staff
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Staff Table Card -->
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-trash-alt me-2"></i>Deleted Staff Members
                        <span class="badge bg-light text-dark ms-2">{{ $staffMembers->total() }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($staffMembers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Staff ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Hire Date</th>
                                        <th>Deleted At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staffMembers as $staff)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $staff->staff_id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <div class="avatar-title rounded-circle bg-light text-danger">
                                                            {{ strtoupper(substr($staff->user->first_name, 0, 1)) }}{{ strtoupper(substr($staff->user->last_name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $staff->full_name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $staff->user->email }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $staff->position ?: 'Not specified' }}</span>
                                            </td>
                                            <td>{{ $staff->hire_date_formatted }}</td>
                                            <td>
                                                <small class="text-muted">{{ $staff->deleted_at->format('M j, Y g:i A') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <!-- Restore Button -->
                                                    <form method="POST" action="{{ route('medical.staff.restore', $staff->id) }}" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" 
                                                                onclick="return confirm('Are you sure you want to restore this staff member?')"
                                                                title="Restore Staff Member">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Permanent Delete Button -->
                                                    <form method="POST" action="{{ route('medical.staff.force-delete', $staff->id) }}" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('Are you sure you want to permanently delete this staff member? This action cannot be undone!')"
                                                                title="Permanently Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $staffMembers->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-trash-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Deleted Staff Members</h5>
                            <p class="text-muted">There are no deleted staff members to display.</p>
                            <a href="{{ route('medical.staff.index') }}" class="btn btn-primary">
                                <i class="fas fa-users me-2"></i>View Active Staff
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}
</style>
@endsection