<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::with([])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('medical.patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('medical.patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        Patient::create($validated);

        return redirect()->route('medical.patients.index')
            ->with('success', 'Patient created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        return view('medical.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        return view('medical.patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
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
        $patient->delete();

        return redirect()->route('medical.patients.index')
            ->with('success', 'Patient archived successfully.');
    }

    /**
     * Display trashed patients.
     */
    public function trashed()
    {
        $patients = Patient::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('medical.patients.trashed', compact('patients'));
    }

    /**
     * Restore a trashed patient.
     */
    public function restore($id)
    {
        $patient = Patient::onlyTrashed()->findOrFail($id);
        $patient->restore();

        return redirect()->route('medical.patients.index')
            ->with('success', 'Patient restored successfully.');
    }

    /**
     * Permanently delete a patient.
     */
    public function forceDelete($id)
    {
        $patient = Patient::onlyTrashed()->findOrFail($id);
        $patient->forceDelete();

        return redirect()->route('medical.patients.trashed')
            ->with('success', 'Patient permanently deleted.');
    }

    /**
     * Toggle patient status.
     */
    public function toggleStatus(Patient $patient)
    {
        $patient->update([
            'status' => $patient->status === 'active' ? 'inactive' : 'active'
        ]);

        return redirect()->back()
            ->with('success', 'Patient status updated successfully.');
    }
}
