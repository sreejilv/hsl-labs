<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $table = 'doctor_details';

    protected $fillable = [
        'user_id',
        'clinic_name',
        'doctor_name',
        'address',
        'phone',
        'documents',
        'is_active',
    ];

    protected $casts = [
        'documents' => 'array',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}