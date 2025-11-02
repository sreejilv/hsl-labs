@extends('layouts.medical')

@section('title', 'Patient Details - ' . $patient->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-injured text-primary me-2"></i>
                        Patient Details
                    </h4>
                    <div class="d-flex gap-2">
                        @if(!isset($isStaff) || !$isStaff)
                            <a href="{{ route('medical.patients.edit', $patient) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        @endif
                        <a href="{{ route('medical.patients.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user me-2"></i>Personal Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Patient ID:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-primary fs-6">{{ $patient->patient_id }}</span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Full Name:</strong></div>
                                        <div class="col-sm-8">{{ $patient->full_name }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Email:</strong></div>
                                        <div class="col-sm-8">{{ $patient->email ?: 'Not provided' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Phone:</strong></div>
                                        <div class="col-sm-8">{{ $patient->phone }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Date of Birth:</strong></div>
                                        <div class="col-sm-8">{{ $patient->date_of_birth->format('F j, Y') }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Age:</strong></div>
                                        <div class="col-sm-8">{{ $patient->age }} years old</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Gender:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-secondary">{{ ucfirst($patient->gender) }}</span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Blood Group:</strong></div>
                                        <div class="col-sm-8">
                                            @if($patient->blood_group)
                                                <span class="badge bg-danger">{{ $patient->blood_group }}</span>
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Status:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-{{ $patient->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($patient->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Address:</strong></div>
                                        <div class="col-sm-8">{{ $patient->address }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">
                                        <i class="fas fa-phone me-2"></i>Emergency Contact
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Contact Name:</strong></div>
                                        <div class="col-sm-8">{{ $patient->emergency_contact_name }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Contact Phone:</strong></div>
                                        <div class="col-sm-8">
                                            <a href="tel:{{ $patient->emergency_contact_phone }}" class="text-decoration-none">
                                                {{ $patient->emergency_contact_phone }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Medical Information -->
                        <div class="col-lg-8 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-notes-medical me-2"></i>Medical Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <strong>Allergies:</strong>
                                            <div class="mt-2">
                                                @if($patient->allergies && count($patient->allergies) > 0)
                                                    @foreach($patient->allergies as $allergy)
                                                        <span class="badge bg-danger me-1">{{ $allergy }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No known allergies</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <strong>Medical History:</strong>
                                            <div class="mt-2">
                                                @if($patient->medical_history && count($patient->medical_history) > 0)
                                                    <ul class="list-unstyled">
                                                        @foreach($patient->medical_history as $history)
                                                            <li><i class="fas fa-chevron-right text-muted me-2"></i>{{ $history }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No medical history recorded</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Current Medications:</strong>
                                            <div class="mt-2">
                                                @if($patient->current_medications && count($patient->current_medications) > 0)
                                                    <ul class="list-unstyled">
                                                        @foreach($patient->current_medications as $medication)
                                                            <li><i class="fas fa-pills text-primary me-2"></i>{{ $medication }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No current medications</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Insurance Information -->
                        <div class="col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-shield-alt me-2"></i>Insurance
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <strong>Provider:</strong>
                                            <div class="mt-1">
                                                {{ $patient->insurance_provider ?: 'Not specified' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Policy Number:</strong>
                                            <div class="mt-1">
                                                @if($patient->insurance_policy_number)
                                                    <code>{{ $patient->insurance_policy_number }}</code>
                                                @else
                                                    <span class="text-muted">Not specified</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-cogs me-2"></i>Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('medical.patients.edit', $patient) }}" class="btn btn-primary">
                                            <i class="fas fa-edit me-1"></i>Edit Patient
                                        </a>
                                        <form action="{{ route('medical.patients.toggle-status', $patient) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-{{ $patient->status === 'active' ? 'warning' : 'success' }}">
                                                <i class="fas fa-{{ $patient->status === 'active' ? 'pause' : 'play' }} me-1"></i>
                                                {{ $patient->status === 'active' ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
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