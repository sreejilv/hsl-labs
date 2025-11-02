@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-users me-2"></i>Staff Management</h1>
                <div>
                    <a href="{{ route('medical.staff.trashed') }}" class="btn btn-outline-danger me-2">
                        <i class="fas fa-trash-alt me-2"></i>Deleted Staff
                    </a>
                    <a href="{{ route('medical.staff.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Add New Staff
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Search and Filter Section -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('medical.staff.index') }}" class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search Staff</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search by name, email, or staff ID...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fas fa-search me-1"></i>Search
                            </button>
                            <a href="{{ route('medical.staff.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Staff Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $staffs->total() }}</h4>
                                    <p class="mb-0">Total Staff</p>
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
                                    <h4>{{ $staffs->where('is_active', true)->count() }}</h4>
                                    <p class="mb-0">Active</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
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
                                    <h4>{{ $staffs->where('is_active', false)->count() }}</h4>
                                    <p class="mb-0">Inactive</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-ban fa-2x"></i>
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
                                    <h4>{{ $staffs->where('shift', 'night')->count() }}</h4>
                                    <p class="mb-0">Night Shift</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-moon fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Table -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-list me-2"></i>Staff List</h4>
                </div>
                <div class="card-body">
                    @if($staffs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Staff ID</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Shift</th>
                                        <th>Hire Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staffs as $staff)
                                    <tr>
                                        <td><code>{{ $staff->staff_id }}</code></td>
                                        <td>
                                            <strong>{{ $staff->full_name }}</strong>
                                            <br><small class="text-muted">{{ $staff->user->email }}</small>
                                        </td>
                                        <td>{{ $staff->position ?: 'Not specified' }}</td>
                                        <td>
                                            @if($staff->shift === 'day')
                                                <span class="badge bg-warning"><i class="fas fa-sun me-1"></i>Day</span>
                                            @elseif($staff->shift === 'night')
                                                <span class="badge bg-dark"><i class="fas fa-moon me-1"></i>Night</span>
                                            @else
                                                <span class="badge bg-info"><i class="fas fa-sync me-1"></i>Rotating</span>
                                            @endif
                                        </td>
                                        <td>{{ $staff->hire_date_formatted }}</td>
                                        <td>
                                            @if($staff->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('medical.staff.show', $staff) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('medical.staff.edit', $staff) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('medical.staff.toggle-status', $staff) }}" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-{{ $staff->is_active ? 'warning' : 'success' }}" 
                                                            title="{{ $staff->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas fa-{{ $staff->is_active ? 'ban' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $staff->id }}" 
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Delete Confirmation Modal -->
                                    <div class="modal fade" id="deleteModal{{ $staff->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete the staff member <strong>{{ $staff->full_name }}</strong>?</p>
                                                    <p class="text-danger"><small>This action cannot be undone and will remove both user account and staff details.</small></p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form method="POST" action="{{ route('medical.staff.destroy', $staff) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete Staff</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $staffs->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No staff members found</h5>
                            <p class="text-muted">Start by adding your first staff member.</p>
                            <a href="{{ route('medical.staff.create') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>Add First Staff Member
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection