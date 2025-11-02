<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StaffDetail::with('user')
            ->where('doctor_id', Auth::id());

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('staff_id', 'like', "%{$search}%")
              ->orWhere('position', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        $staffs = $query->latest()->paginate(10);

        return view('medical.staff.index', compact('staffs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = [
            'Nursing', 'Laboratory', 'Radiology', 'Pharmacy', 
            'Administration', 'Maintenance', 'Security', 'Reception'
        ];
        
        $positions = [
            'Registered Nurse', 'Licensed Practical Nurse', 'Nurse Assistant',
            'Lab Technician', 'Lab Assistant', 'Radiologic Technologist',
            'Pharmacy Technician', 'Administrative Assistant', 'Receptionist',
            'Maintenance Worker', 'Security Officer', 'Cleaner'
        ];

        return view('medical.staff.create', compact('departments', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'staff_id' => 'required|string|max:255|unique:staff_details',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'shift' => 'required|in:day,night,rotating',
            'is_active' => 'boolean'
        ]);

        DB::transaction(function () use ($request) {
            // Create user
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
            ]);

            // Assign staff role
            $user->assignRole('staff');

            // Create staff details
            StaffDetail::create([
                'user_id' => $user->id,
                'staff_id' => $request->staff_id,
                'department' => null, // We removed department from forms but keep for database compatibility
                'position' => $request->position,
                'hire_date' => $request->hire_date,
                'salary' => $request->salary,
                'shift' => $request->shift ?: 'day', // Default to 'day' if empty
                'is_active' => $request->boolean('is_active', true),
                'created_by' => Auth::id(), // Track who created this staff member
                'doctor_id' => Auth::id(), // Assign to authenticated doctor
            ]);
        });

        return redirect()->route('medical.staff.index')
                        ->with('success', 'Staff member created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(StaffDetail $staff)
    {
        // Ensure the staff belongs to the authenticated doctor
        if ($staff->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to staff record.');
        }

        $staff->load('user');
        return view('medical.staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StaffDetail $staff)
    {
        // Ensure the staff belongs to the authenticated doctor
        if ($staff->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to staff record.');
        }

        $staff->load('user');
        $departments = [
            'Nursing', 'Laboratory', 'Radiology', 'Pharmacy', 
            'Administration', 'Maintenance', 'Security', 'Reception'
        ];
        
        $positions = [
            'Registered Nurse', 'Licensed Practical Nurse', 'Nurse Assistant',
            'Lab Technician', 'Lab Assistant', 'Radiologic Technologist',
            'Pharmacy Technician', 'Administrative Assistant', 'Receptionist',
            'Maintenance Worker', 'Security Officer', 'Cleaner'
        ];

        return view('medical.staff.edit', compact('staff', 'departments', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StaffDetail $staff)
    {
        // Ensure the staff belongs to the authenticated doctor
        if ($staff->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to staff record.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $staff->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'staff_id' => 'required|string|max:255|unique:staff_details,staff_id,' . $staff->id,
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'shift' => 'nullable|in:day,night,rotating',
            'is_active' => 'boolean'
        ]);

        DB::transaction(function () use ($request, $staff) {
            // Update user
            $userData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $staff->user->update($userData);

            // Update staff details
            $staff->update([
                'staff_id' => $request->staff_id,
                'department' => null, // We removed department from forms but keep for database compatibility
                'position' => $request->position,
                'hire_date' => $request->hire_date,
                'salary' => $request->salary,
                'shift' => $request->shift,
                'is_active' => $request->boolean('is_active', true),
            ]);
        });

        return redirect()->route('medical.staff.show', $staff)
                        ->with('success', 'Staff member updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StaffDetail $staff)
    {
        // Ensure the staff belongs to the authenticated doctor
        if ($staff->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to staff record.');
        }

        DB::transaction(function () use ($staff) {
            // Soft delete the staff detail (this will also deactivate the user account)
            $staff->user->update(['is_active' => false]);
            $staff->delete(); // This is now a soft delete
        });

        return redirect()->route('medical.staff.index')
                        ->with('success', 'Staff member deleted successfully!');
    }

    /**
     * Toggle staff status (active/inactive)
     */
    public function toggleStatus(StaffDetail $staff)
    {
        // Ensure the staff belongs to the authenticated doctor
        if ($staff->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to staff record.');
        }

        $staff->update(['is_active' => !$staff->is_active]);

        $status = $staff->is_active ? 'activated' : 'deactivated';
        return redirect()->route('medical.staff.index')
                        ->with('success', "Staff member {$status} successfully!");
    }

    /**
     * Show deleted staff members.
     */
    public function trashed(Request $request)
    {
        $query = StaffDetail::onlyTrashed()
            ->where('doctor_id', Auth::id())
            ->with(['user', 'createdBy'])
            ->latest('deleted_at');

        // Search functionality for trashed items
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('staff_id', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $staffMembers = $query->paginate(15);

        return view('medical.staff.trashed', compact('staffMembers'));
    }

    /**
     * Restore a soft deleted staff member.
     */
    public function restore($id)
    {
        $staff = StaffDetail::onlyTrashed()
            ->where('doctor_id', Auth::id())
            ->findOrFail($id);
        
        $staff->restore();

        // Reactivate the user account
        $staff->user->update(['is_active' => true]);

        return redirect()->route('medical.staff.trashed')
                        ->with('success', 'Staff member restored successfully!');
    }

    /**
     * Force delete a staff member permanently.
     */
    public function forceDelete($id)
    {
        $staff = StaffDetail::onlyTrashed()
            ->where('doctor_id', Auth::id())
            ->findOrFail($id);
        
        // Permanently delete the user as well
        $user = $staff->user;
        $staff->forceDelete();
        $user->forceDelete();

        return redirect()->route('medical.staff.trashed')
                        ->with('success', 'Staff member permanently deleted!');
    }
}
