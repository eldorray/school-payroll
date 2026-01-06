<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'joined_at' => 'date',
        'is_active' => 'boolean',
        'is_tahfidz' => 'boolean',
    ];

    public function allowances()
    {
        return $this->hasMany(TeacherAllowance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function annualSettings()
    {
        return $this->hasMany(TeacherAnnualSetting::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Calculate work tenure (masa kerja) based on joined_at date
     *
     * @param \Carbon\Carbon|null $referenceDate Reference date to calculate from (default: now)
     * @return array ['years' => int, 'months' => int]
     */
    public function calculateTenure($referenceDate = null)
    {
        if (!$this->joined_at) {
            return ['years' => 0, 'months' => 0];
        }

        $referenceDate = $referenceDate ?? now();
        
        $diff = $this->joined_at->diff($referenceDate);
        
        return [
            'years' => $diff->y,
            'months' => $diff->m,
        ];
    }

    /**
     * Get formatted tenure string
     *
     * @param \Carbon\Carbon|null $referenceDate
     * @return string
     */
    public function getTenureFormatted($referenceDate = null)
    {
        $tenure = $this->calculateTenure($referenceDate);
        
        if ($tenure['years'] === 0 && $tenure['months'] === 0) {
            return '-';
        }

        $parts = [];
        if ($tenure['years'] > 0) {
            $parts[] = $tenure['years'] . ' tahun';
        }
        if ($tenure['months'] > 0) {
            $parts[] = $tenure['months'] . ' bulan';
        }
        
        return implode(' ', $parts);
    }
}
