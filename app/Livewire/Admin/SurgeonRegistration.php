<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\DoctorDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class SurgeonRegistration extends Component
{
    use WithFileUploads;

    public $clinic_name = '';
    public $doctor_name = '';
    public $address = '';
    public $email = '';
    public $phone = '';
    public $documents = null; // Changed to single file
    public $password = '';
    public $password_confirmation = '';

    protected function rules()
    {
        return [
            'clinic_name' => 'required|string|max:255',
            'doctor_name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'documents' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max, nullable for optional
        ];
    }

    protected $messages = [
        'clinic_name.required' => 'Clinic name is required.',
        'doctor_name.required' => 'Doctor name is required.',
        'address.required' => 'Address is required.',
        'email.required' => 'Email is required.',
        'email.unique' => 'This email is already registered.',
        'phone.required' => 'Phone number is required.',
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 6 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
        'documents.mimes' => 'Document must be a PDF, Word document, or image file.',
        'documents.max' => 'Document size cannot exceed 5MB.',
    ];

    public function register()
    {
        // Validate required fields first
        $this->validate([
            'clinic_name' => 'required|string|max:255',
            'doctor_name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        try {
            // Create user
            $user = User::create([
                'name' => $this->doctor_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            // Assign surgeon role
            $user->assignRole('surgeon');

            // Handle document upload - if it fails, just skip it but continue registration
            $documentPaths = [];
            if ($this->documents) {
                try {
                    // Validate individual document
                    if ($this->documents->getSize() <= 5120 * 1024 && // 5MB in bytes
                        in_array($this->documents->getMimeType(), [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'image/jpeg',
                            'image/png'
                        ])) {
                        
                        $filename = time() . '_' . uniqid() . '.' . $this->documents->getClientOriginalExtension();
                        $path = $this->documents->storeAs('surgeon-documents', $filename, 'public');
                        
                        $documentPaths[] = [
                            'filename' => $filename,
                            'original_name' => $this->documents->getClientOriginalName(),
                            'size' => $this->documents->getSize(),
                            'mime_type' => $this->documents->getMimeType(),
                        ];
                    }
                    // If document validation fails, just skip it - don't break the registration
                } catch (\Exception $e) {
                    // Log error but continue with registration
                    Log::warning('Document upload failed during surgeon registration: ' . $e->getMessage());
                }
            }

            // Create doctor detail
            DoctorDetail::create([
                'user_id' => $user->id,
                'clinic_name' => $this->clinic_name,
                'doctor_name' => $this->doctor_name,
                'address' => $this->address,
                'phone' => $this->phone,
                'documents' => $documentPaths,
                'is_active' => true,
            ]);

            // Clear form
            $this->reset(['clinic_name', 'doctor_name', 'address', 'email', 'phone', 'password', 'password_confirmation', 'documents']);

            session()->flash('success', 'Doctor registered successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.surgeon-registration');
    }
}
