<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function payrollSettings()
    {
        return $this->hasMany(PayrollSetting::class);
    }

    /**
     * Get payroll settings for a specific unit
     */
    public function getSettingsForUnit($unitId)
    {
        return $this->payrollSettings()->where('unit_id', $unitId)->first();
    }

    public function teacherAllowances()
    {
        return $this->hasMany(TeacherAllowance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function teacherAnnualSettings()
    {
        return $this->hasMany(TeacherAnnualSetting::class);
    }
}
