<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-users me-2"></i>Registered Doctors</h4>
                        <a href="{{ route('admin.surgeon.register') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus me-1"></i>Add New Doctor
                        </a>
                    </div>
                    <div class="card-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Search and Filter Controls -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input wire:model.live="search" type="text" class="form-control" 
                                           placeholder="Search by name, clinic, email, or phone...">
                                </div>
                            </div>
                            <div class="col-md-6 d-flex justify-content-end align-items-center">
                                <label class="me-2">Show:</label>
                                <select wire:model.live="perPage" class="form-select" style="width: auto;">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="ms-2">entries</span>
                            </div>
                        </div>

                        <!-- Doctors Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Doctor Name</th>
                                        <th>Clinic Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Documents</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($surgeons as $index => $surgeon)
                                        <tr>
                                            <td>{{ $surgeons->firstItem() + $index }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-md text-primary me-2"></i>
                                                    <strong>{{ $surgeon->doctorDetail->doctor_name ?? 'N/A' }}</strong>
                                                </div>
                                            </td>
                                            <td>{{ $surgeon->doctorDetail->clinic_name ?? 'N/A' }}</td>
                                            <td>
                                                <a href="mailto:{{ $surgeon->email }}" class="text-decoration-none">
                                                    {{ $surgeon->email }}
                                                </a>
                                            </td>
                                            <td>{{ $surgeon->doctorDetail->phone ?? 'N/A' }}</td>
                                            <td>
                                                @if($surgeon->doctorDetail && $surgeon->doctorDetail->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                                                                @if($surgeon->doctorDetail && $surgeon->doctorDetail->documents && count($surgeon->doctorDetail->documents) > 0)
                                                    <button wire:click="viewDocuments({{ $surgeon->id }})" 
                                                            class="btn btn-sm btn-outline-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#documentsModal">
                                                        <i class="fas fa-file-alt me-1"></i>
                                                        {{ count($surgeon->doctorDetail->documents) }} file(s)
                                                    </button>
                                                @else
                                                    <span class="text-muted">No documents</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $surgeon->created_at->format('M d, Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button wire:click="toggleStatus({{ $surgeon->id }})" 
                                                            class="btn btn-sm {{ $surgeon->doctorDetail && $surgeon->doctorDetail->is_active ? 'btn-warning' : 'btn-success' }}"
                                                            wire:confirm="Are you sure you want to {{ $surgeon->doctorDetail && $surgeon->doctorDetail->is_active ? 'deactivate' : 'activate' }} this doctor?">
                                                        @if($surgeon->doctorDetail && $surgeon->doctorDetail->is_active)
                                                            <i class="fas fa-user-slash"></i>
                                                        @else
                                                            <i class="fas fa-user-check"></i>
                                                        @endif
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-users fa-3x mb-3"></i>
                                                    <h5>No doctors found</h5>
                                                    <p>No doctors match your search criteria.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($surgeons->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Showing {{ $surgeons->firstItem() }} to {{ $surgeons->lastItem() }} of {{ $surgeons->total() }} results
                                </div>
                                <div>
                                    {{ $surgeons->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Modal -->
    <div wire:ignore.self class="modal fade" id="documentsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-alt me-2"></i>Doctor Documents
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($documents && count($documents) > 0)
                        <div class="list-group">
                            @foreach($documents as $document)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger me-3"></i>
                                        <div>
                                            <h6 class="mb-0">{{ $document['original_name'] }}</h6>
                                            <small class="text-muted">{{ round($document['size'] / 1024, 2) }} KB</small>
                                        </div>
                                    </div>
                                    <div>
                                        <button wire:click="downloadDocument('{{ $document['filename'] }}')" 
                                                class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i>Download
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No documents available</h5>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
