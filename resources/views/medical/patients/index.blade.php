@extends('layouts.medical')

@section('title', 'Patient List')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-injured text-primary me-2"></i>
                        Patient List
                        @if(isset($isStaff) && $isStaff && isset($doctorName))
                            <small class="text-muted d-block">Viewing patients of Dr. {{ $doctorName }}</small>
                        @endif
                    </h4>
                    <div class="d-flex gap-2">
                        @if(!isset($isStaff) || !$isStaff)
                            <a href="{{ route('medical.patients.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add Patient
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(isset($isStaff) && $isStaff)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You are viewing patients assigned to your supervising doctor. You can view patient details but cannot create, edit, or modify patient records.
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
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patients as $patient)
                                        <tr>
                                            <td>
                                                <strong class="text-primary">{{ $patient->patient_id }}</strong>
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
                                                <span class="badge bg-{{ $patient->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($patient->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('medical.patients.show', $patient) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(!isset($isStaff) || !$isStaff)
                                                        <a href="{{ route('medical.patients.edit', $patient) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('medical.patients.toggle-status', $patient) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-{{ $patient->status === 'active' ? 'warning' : 'success' }}" 
                                                                    title="{{ $patient->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                                <i class="fas fa-{{ $patient->status === 'active' ? 'pause' : 'play' }}"></i>
                                                            </button>
                                                        </form>
                                                    @endif
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
                            <i class="fas fa-user-injured fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No patients found</h5>
                            <p class="text-muted">Start by adding your first patient to the system.</p>
                            <a href="{{ route('medical.patients.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add First Patient
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection