<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\StaffDetail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Get the doctor ID for the current user (either the doctor themselves or their assigned doctor if staff)
     */
    private function getDoctorId()
    {
        $user = Auth::user();
        
        // Check if user is a surgeon
        if ($user->hasRole('surgeon')) {
            return $user->id;
        } 
        // Check if user is staff
        elseif ($user->hasRole('staff')) {
            $staffDetail = StaffDetail::where('user_id', $user->id)->first();
            return $staffDetail ? $staffDetail->doctor_id : null;
        }
        
        return null;
    }

    /**
     * Check if the current user can access a specific patient
     */
    private function canAccessPatient(Patient $patient)
    {
        $doctorId = $this->getDoctorId();
        return $doctorId && $patient->doctor_id === $doctorId;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctorId = $this->getDoctorId();
        
        if (!$doctorId) {
            abort(403, 'Access denied. No doctor assigned.');
        }
        
        $patients = Patient::with(['doctor'])
            ->where('doctor_id', $doctorId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get current user info for display purposes
        $user = Auth::user();
        $isStaff = $user->hasRole('staff');
        $doctorName = null;
        
        if ($isStaff) {
            $doctor = \App\Models\User::find($doctorId);
            $doctorName = $doctor ? $doctor->first_name . ' ' . $doctor->last_name : 'Unknown Doctor';
        }

        return view('medical.patients.index', compact('patients', 'isStaff', 'doctorName'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $doctorId = $this->getDoctorId();
        
        if (!$doctorId) {
            abort(403, 'Access denied. No doctor assigned.');
        }
        
        // Only doctors can create patients, not staff
        $user = Auth::user();
        if ($user->hasRole('staff')) {
            return redirect()->route('medical.patients.index')
                ->with('error', 'Only doctors can create new patients.');
        }
        
        return view('medical.patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only doctors can create patients, not staff
        $user = Auth::user();
        if ($user->hasRole('staff')) {
            return redirect()->route('medical.patients.index')
                ->with('error', 'Only doctors can create new patients.');
        }
        
        $doctorId = $this->getDoctorId();
        
        if (!$doctorId) {
            abort(403, 'Access denied. No doctor assigned.');
        }
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'blood_group' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        // Convert text fields to arrays if they contain data
        $validated['allergies'] = $validated['allergies'] ? array_filter(array_map('trim', explode(',', $validated['allergies']))) : null;
        $validated['medical_history'] = $validated['medical_history'] ? array_filter(array_map('trim', explode(',', $validated['medical_history']))) : null;
        $validated['current_medications'] = $validated['current_medications'] ? array_filter(array_map('trim', explode(',', $validated['current_medications']))) : null;

        // Assign the authenticated doctor
        $validated['doctor_id'] = $doctorId;

        Patient::create($validated);

        return redirect()->route('medical.patients.index')
            ->with('success', 'Patient created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        // Ensure the patient belongs to the authenticated doctor or staff's assigned doctor
        if (!$this->canAccessPatient($patient)) {
            abort(403, 'Unauthorized access to patient record.');
        }

        // Check if current user is staff
        $user = Auth::user();
        $isStaff = $user->hasRole('staff');

        return view('medical.patients.show', compact('patient', 'isStaff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        // Ensure the patient belongs to the authenticated doctor or staff's assigned doctor
        if (!$this->canAccessPatient($patient)) {
            abort(403, 'Unauthorized access to patient record.');
        }
        
        // Only doctors can edit patients, not staff
        $user = Auth::user();
        if ($user->hasRole('staff')) {
            return redirect()->route('medical.patients.show', $patient)
                ->with('error', 'Only doctors can edit patient records.');
        }

        return view('medical.patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        // Ensure the patient belongs to the authenticated doctor or staff's assigned doctor
        if (!$this->canAccessPatient($patient)) {
            abort(403, 'Unauthorized access to patient record.');
        }
        
        // Only doctors can update patients, not staff
        $user = Auth::user();
        if ($user->hasRole('staff')) {
            return redirect()->route('medical.patients.show', $patient)
                ->with('error', 'Only doctors can update patient records.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('patients')->ignore($patient->id)],
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'blood_group' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        // Convert text fields to arrays if they contain data
        $validated['allergies'] = $validated['allergies'] ? array_filter(array_map('trim', explode(',', $validated['allergies']))) : null;
        $validated['medical_history'] = $validated['medical_history'] ? array_filter(array_map('trim', explode(',', $validated['medical_history']))) : null;
        $validated['current_medications'] = $validated['current_medications'] ? array_filter(array_map('trim', explode(',', $validated['current_medications']))) : null;

        $patient->update($validated);

        return redirect()->route('medical.patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        // Ensure the patient belongs to the authenticated doctor or staff's assigned doctor
        if (!$this->canAccessPatient($patient)) {
            abort(403, 'Unauthorized access to patient record.');
        }
        
        // Only doctors can delete patients, not staff
        $user = Auth::user();
        if ($user->hasRole('staff')) {
            return redirect()->route('medical.patients.index')
                ->with('error', 'Only doctors can delete patient records.');
        }

        // Archive functionality removed as per requirements
        return redirect()->route('medical.patients.index')
            ->with('info', 'Archive functionality has been disabled.');
    }

    /**
     * Toggle patient status.
     */
    public function toggleStatus(Patient $patient)
    {
        // Ensure the patient belongs to the authenticated doctor or staff's assigned doctor
        if (!$this->canAccessPatient($patient)) {
            abort(403, 'Unauthorized access to patient record.');
        }
        
        // Only doctors can toggle patient status, not staff
        $user = Auth::user();
        if ($user->hasRole('staff')) {
            return redirect()->back()
                ->with('error', 'Only doctors can change patient status.');
        }

        $patient->update([
            'status' => $patient->status === 'active' ? 'inactive' : 'active'
        ]);

        return redirect()->back()
            ->with('success', 'Patient status updated successfully.');
    }
}
