<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-cog me-2"></i>System Settings</h4>
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

                        <!-- Settings Tabs -->
                        <ul class="nav nav-tabs mb-4" id="settingsTabs">
                            <li class="nav-item">
                                <button class="nav-link {{ $activeTab === 'company' ? 'active' : '' }}" 
                                        wire:click="setActiveTab('company')">
                                    <i class="fas fa-building me-2"></i>Company Details
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link {{ $activeTab === 'password' ? 'active' : '' }}" 
                                        wire:click="setActiveTab('password')">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                            </li>
                        </ul>

                        <!-- Company Details Tab -->
                        @if($activeTab === 'company')
                            <form wire:submit="updateCompanySettings">
                                <div class="row">
                                    <!-- Company Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                            <input wire:model="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                                   id="company_name" placeholder="Enter company name" required>
                                        </div>
                                        @error('company_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Company Email -->
                                    <div class="col-md-6 mb-3">
                                        <label for="company_email" class="form-label">Company Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input wire:model="company_email" type="email" class="form-control @error('company_email') is-invalid @enderror" 
                                                   id="company_email" placeholder="company@example.com" required>
                                        </div>
                                        @error('company_email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Company Phone -->
                                    <div class="col-md-6 mb-3">
                                        <label for="company_phone" class="form-label">Company Phone <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input wire:model="company_phone" type="tel" class="form-control @error('company_phone') is-invalid @enderror" 
                                                   id="company_phone" placeholder="+1 234 567 8900" required>
                                        </div>
                                        @error('company_phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Company Website -->
                                    <div class="col-md-6 mb-3">
                                        <label for="company_website" class="form-label">Company Website</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                            <input wire:model="company_website" type="url" class="form-control @error('company_website') is-invalid @enderror" 
                                                   id="company_website" placeholder="https://company.com">
                                        </div>
                                        @error('company_website') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Company Address -->
                                    <div class="col-12 mb-3">
                                        <label for="company_address" class="form-label">Company Address <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            <textarea wire:model="company_address" class="form-control @error('company_address') is-invalid @enderror" 
                                                      id="company_address" rows="3" placeholder="Full company address" required></textarea>
                                        </div>
                                        @error('company_address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Company Description -->
                                    <div class="col-12 mb-3">
                                        <label for="company_description" class="form-label">Company Description</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                            <textarea wire:model="company_description" class="form-control @error('company_description') is-invalid @enderror" 
                                                      id="company_description" rows="3" placeholder="Brief description of the company"></textarea>
                                        </div>
                                        @error('company_description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Company Logo -->
                                    <div class="col-12 mb-3">
                                        <label for="company_logo" class="form-label">Company Logo</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-image"></i></span>
                                            <input wire:model="company_logo" type="file" class="form-control @error('company_logo') is-invalid @enderror" 
                                                   id="company_logo" accept="image/jpeg,image/jpg,image/png,image/gif">
                                            @if($company_logo)
                                                <button type="button" class="btn btn-outline-danger" wire:click="removeCompanyLogo">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                        
                                        @if($company_logo)
                                            <div class="mt-2">
                                                @error('company_logo')
                                                    <div class="d-flex align-items-center text-danger">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <span>{{ $company_logo->getClientOriginalName() }} - Error: File not valid</span>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center text-success">
                                                        <i class="fas fa-check-circle me-2"></i>
                                                        <span>{{ $company_logo->getClientOriginalName() }} ({{ number_format($company_logo->getSize() / 1024, 2) }} KB)</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        @endif
                                        
                                        <div class="form-text">
                                            <small><i class="fas fa-info-circle me-1"></i>Accepted formats: JPEG, JPG, PNG, GIF. Maximum size: 2MB.</small>
                                        </div>
                                        
                                        @error('company_logo') 
                                            <div class="invalid-feedback d-block">
                                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                            </div> 
                                        @enderror
                                        
                                        <div wire:loading wire:target="company_logo" class="text-info mt-2">
                                            <i class="fas fa-spinner fa-spin me-1"></i>Uploading file...
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-12">
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary" 
                                                    wire:loading.attr="disabled"
                                                    @if($errors->has('company_logo')) disabled @endif>
                                                <span wire:loading.remove wire:target="updateCompanySettings">
                                                    <i class="fas fa-save me-1"></i>Update Company Settings
                                                </span>
                                                <span wire:loading wire:target="updateCompanySettings">
                                                    <i class="fas fa-spinner fa-spin me-1"></i>Updating...
                                                </span>
                                            </button>
                                        </div>
                                        @if($errors->has('company_logo'))
                                            <div class="text-end mt-2">
                                                <small class="text-danger">
                                                    <i class="fas fa-info-circle me-1"></i>Please fix the file upload error to submit the form.
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        @endif

                        <!-- Change Password Tab -->
                        @if($activeTab === 'password')
                            <form wire:submit="updatePassword">
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <!-- Current Password -->
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input wire:model="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                                       id="current_password" placeholder="Enter current password" required>
                                            </div>
                                            @error('current_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>

                                        <!-- New Password -->
                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                <input wire:model="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                                       id="new_password" placeholder="Enter new password (min 6 characters)" required>
                                            </div>
                                            @error('new_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>

                                        <!-- Confirm New Password -->
                                        <div class="mb-3">
                                            <label for="new_password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                <input wire:model="new_password_confirmation" type="password" class="form-control" 
                                                       id="new_password_confirmation" placeholder="Confirm new password" required>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-warning" wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="updatePassword">
                                                    <i class="fas fa-shield-alt me-1"></i>Update Password
                                                </span>
                                                <span wire:loading wire:target="updatePassword">
                                                    <i class="fas fa-spinner fa-spin me-1"></i>Updating...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
