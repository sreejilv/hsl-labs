@extends('layouts.medical')

@section('title', 'Archived Patients')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-archive text-warning me-2"></i>
                        Archived Patients
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('medical.patients.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Active Patients
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($patients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Patient ID</th>
                                        <th>Name</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Phone</th>
                                        <th>Blood Group</th>
                                        <th>Archived Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patients as $patient)
                                        <tr>
                                            <td>
                                                <strong class="text-muted">{{ $patient->patient_id }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $patient->full_name }}</strong>
                                                    @if($patient->email)
                                                        <br><small class="text-muted">{{ $patient->email }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $patient->age ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($patient->gender) }}
                                                </span>
                                            </td>
                                            <td>{{ $patient->phone }}</td>
                                            <td>
                                                @if($patient->blood_group)
                                                    <span class="badge bg-danger">{{ $patient->blood_group }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $patient->deleted_at->format('M j, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <form action="{{ route('medical.patients.restore', $patient->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-success" 
                                                                title="Restore Patient"
                                                                onclick="return confirm('Are you sure you want to restore this patient?')">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('medical.patients.force-delete', $patient->id) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to permanently delete this patient? This action cannot be undone!')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Permanently">
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
                        <div class="d-flex justify-content-center">
                            {{ $patients->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No archived patients</h5>
                            <p class="text-muted">Archived patients will appear here when you archive them from the active patients list.</p>
                            <a href="{{ route('medical.patients.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Active Patients
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection