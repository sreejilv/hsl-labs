<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function doctorDetail()
    {
        return $this->hasOne(DoctorDetail::class);
    }

    public function staffDetail()
    {
        return $this->hasOne(StaffDetail::class);
    }

    /**
     * Get the patients that belong to this doctor
     */
    public function patients()
    {
        return $this->hasMany(Patient::class, 'doctor_id');
    }

    /**
     * Get the staff members that belong to this doctor
     */
    public function staffMembers()
    {
        return $this->hasMany(StaffDetail::class, 'doctor_id');
    }

    /**
     * Get the purchase orders created by this doctor
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'doctor_id');
    }

    /**
     * Get the purchase orders confirmed by this admin
     */
    public function confirmedPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'confirmed_by');
    }

    /**
     * Get the sales orders for this doctor's inventory
     */
    public function doctorSalesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'doctor_id');
    }

    /**
     * Get the sales orders created by this staff member
     */
    public function staffSalesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'staff_id');
    }
}
