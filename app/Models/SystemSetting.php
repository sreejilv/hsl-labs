<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'company_name',
        'company_address',
        'company_email',
        'company_phone',
        'company_website',
        'company_description',
        'company_logo',
    ];

    /**
     * Get the system settings (singleton pattern)
     */
    public static function getSettings()
    {
        return self::first() ?? new self();
    }

    /**
     * Update or create system settings
     */
    public static function updateSettings(array $data)
    {
        $settings = self::first();
        
        if ($settings) {
            $settings->update($data);
        } else {
            $settings = self::create($data);
        }
        
        return $settings;
    }
}
