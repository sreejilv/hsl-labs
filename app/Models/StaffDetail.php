<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'staff_id',
        'department',
        'position',
        'hire_date',
        'salary',
        'shift',
        'is_active',
        'emergency_contact_name',
        'emergency_contact_phone',
        'qualifications',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
        'qualifications' => 'array'
    ];

    /**
     * Get the user that owns the staff detail
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created this staff record
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get only active staff
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only inactive staff
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Get staff by department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Get full name from user relationship
     */
    public function getFullNameAttribute()
    {
        return $this->user ? ($this->user->first_name . ' ' . $this->user->last_name) : 'N/A';
    }

    /**
     * Get formatted hire date
     */
    public function getHireDateFormattedAttribute()
    {
        return $this->hire_date ? $this->hire_date->format('F j, Y') : 'Not specified';
    }

    /**
     * Get years of service
     */
    public function getYearsOfServiceAttribute()
    {
        return $this->hire_date ? $this->hire_date->diffInYears(now()) : 0;
    }

    /**
     * Get tenure in human readable format
     */
    public function getTenureAttribute()
    {
        if (!$this->hire_date) {
            return 'N/A';
        }
        
        return $this->hire_date->diffForHumans(null, true);
    }

    /**
     * Get tenure in days
     */
    public function getTenureDaysAttribute()
    {
        if (!$this->hire_date) {
            return 0;
        }
        
        return $this->hire_date->diffInDays(now());
    }
}
