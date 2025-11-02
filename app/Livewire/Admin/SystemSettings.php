<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.admin')]
class SystemSettings extends Component
{
    use WithFileUploads;

    // Company Details
    public $company_name;
    public $company_address;
    public $company_email;
    public $company_phone;
    public $company_website;
    public $company_description;
    public $company_logo;

    // Password Change
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    // Active tab
    public $activeTab = 'company';

    protected $messages = [
        'company_logo.image' => 'The logo must be an image file.',
        'company_logo.mimes' => 'The logo must be a JPEG, JPG, PNG, or GIF file.',
        'company_logo.max' => 'The logo file size must not exceed 2MB.',
        'company_name.required' => 'Company name is required.',
        'company_email.required' => 'Company email is required.',
        'company_email.email' => 'Please enter a valid email address.',
        'company_phone.required' => 'Company phone number is required.',
        'company_address.required' => 'Company address is required.',
        'company_website.url' => 'Please enter a valid website URL.',
        'current_password.required' => 'Current password is required.',
        'new_password.required' => 'New password is required.',
        'new_password.min' => 'New password must be at least 6 characters.',
        'new_password.confirmed' => 'Password confirmation does not match.',
    ];

    protected function rules()
    {
        $rules = [];
        
        if ($this->activeTab === 'company') {
            $rules = [
                'company_name' => 'required|string|max:255',
                'company_address' => 'required|string|max:500',
                'company_email' => 'required|email|max:255',
                'company_phone' => 'required|string|max:20',
                'company_website' => 'nullable|url|max:255',
                'company_description' => 'nullable|string|max:1000',
                'company_logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048', // 2MB max with specific types
            ];
        } elseif ($this->activeTab === 'password') {
            $rules = [
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
                'new_password_confirmation' => 'required',
            ];
        }
        
        return $rules;
    }

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $settings = SystemSetting::getSettings();
        
        $this->company_name = $settings->company_name;
        $this->company_address = $settings->company_address;
        $this->company_email = $settings->company_email;
        $this->company_phone = $settings->company_phone;
        $this->company_website = $settings->company_website;
        $this->company_description = $settings->company_description;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetValidation();
        
        // Reset password fields when switching tabs
        if ($tab !== 'password') {
            $this->current_password = '';
            $this->new_password = '';
            $this->new_password_confirmation = '';
        }
    }

    public function updatedCompanyLogo()
    {
        // Real-time file validation
        if ($this->company_logo) {
            try {
                $this->validate([
                    'company_logo' => 'image|mimes:jpeg,jpg,png,gif|max:2048'
                ]);
                // Clear any previous errors if validation passes
                $this->resetErrorBag('company_logo');
            } catch (\Illuminate\Validation\ValidationException $e) {
                // Handle specific file upload errors
                $errors = $e->validator->errors();
                if ($errors->has('company_logo')) {
                    $this->addError('company_logo', $errors->first('company_logo'));
                }
                // Don't reset the file input - let user see the error with their selected file
                // Only reset if they manually remove it
            }
        }
    }

    public function removeCompanyLogo()
    {
        $this->company_logo = null;
        $this->resetErrorBag('company_logo');
        // Clear any session flash messages related to file upload
        session()->forget('error');
    }

    private function checkUploadErrors()
    {
        $uploadError = $_FILES['company_logo']['error'] ?? UPLOAD_ERR_OK;
        
        switch ($uploadError) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->addError('company_logo', 'The uploaded file is too large. Maximum size allowed is 2MB.');
                return false;
            case UPLOAD_ERR_PARTIAL:
                $this->addError('company_logo', 'The file was only partially uploaded. Please try again.');
                return false;
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->addError('company_logo', 'Missing temporary folder. Please contact administrator.');
                return false;
            case UPLOAD_ERR_CANT_WRITE:
                $this->addError('company_logo', 'Failed to write file to disk. Please contact administrator.');
                return false;
            case UPLOAD_ERR_EXTENSION:
                $this->addError('company_logo', 'File upload stopped by extension. Please contact administrator.');
                return false;
        }
        
        return true;
    }

    public function updateCompanySettings()
    {
        // Check for existing file validation errors first
        if ($this->getErrorBag()->has('company_logo')) {
            // Don't proceed if there are file validation errors
            session()->flash('error', 'Please fix the file upload errors before submitting.');
            return;
        }
        
        // Check for upload errors first if file is present
        if ($this->company_logo && !$this->checkUploadErrors()) {
            return;
        }
        
        // Validate file upload if present
        if ($this->company_logo) {
            try {
                $this->validate([
                    'company_logo' => 'required|image|mimes:jpeg,jpg,png,gif|max:2048'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                // File validation failed - errors are already set
                session()->flash('error', 'Please fix the file upload errors before submitting.');
                return;
            }
        }
        
        // Validate all other fields
        $this->validate($this->rules());

        try {
            $data = [
                'company_name' => $this->company_name,
                'company_address' => $this->company_address,
                'company_email' => $this->company_email,
                'company_phone' => $this->company_phone,
                'company_website' => $this->company_website,
                'company_description' => $this->company_description,
            ];

            // Handle logo upload with better error handling
            if ($this->company_logo) {
                try {
                    // Delete old logo if exists
                    $currentSettings = SystemSetting::getSettings();
                    if ($currentSettings->company_logo && Storage::disk('public')->exists($currentSettings->company_logo)) {
                        Storage::disk('public')->delete($currentSettings->company_logo);
                    }
                    
                    // Store new logo
                    $logoPath = $this->company_logo->store('company-logos', 'public');
                    if (!$logoPath) {
                        $this->addError('company_logo', 'Failed to save the uploaded file. Please try again.');
                        return;
                    }
                    $data['company_logo'] = $logoPath;
                    
                } catch (\Exception $e) {
                    $this->addError('company_logo', 'Failed to upload logo: ' . $e->getMessage());
                    return;
                }
            }

            SystemSetting::updateSettings($data);

            session()->flash('success', 'Company settings updated successfully!');
            
            // Reset the logo input only on successful update
            $this->company_logo = null;
            $this->resetErrorBag('company_logo');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to show field-specific errors
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update company settings: ' . $e->getMessage());
        }
    }

    public function updatePassword()
    {
        $this->validate($this->rules());

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        try {
            $user->update([
                'password' => Hash::make($this->new_password)
            ]);

            // Reset password fields
            $this->current_password = '';
            $this->new_password = '';
            $this->new_password_confirmation = '';

            session()->flash('success', 'Password updated successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update password: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.system-settings');
    }
}
