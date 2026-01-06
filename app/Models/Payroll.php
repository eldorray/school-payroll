<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'details' => 'array',
        'is_tahfidz' => 'boolean',
    ];

    // Helpers for specific deduction logic if needed
    public function getNetSalaryAttribute()
    {
        // total_salary in DB is the Gross - Deductions? Or Gross?
        // Let's assume total_salary stored in DB is the FINAL amount since that's what was done previously.
        // But with deductions, we should be careful.
        // The previous logic was: Total = Teaching + Transport + Allowances.
        // New Logic: Total = (Teaching + Transport + Allowances + Tenure) - Deductions.
        
        return $this->total_salary;
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function batch()
    {
        return $this->belongsTo(PayrollBatch::class, 'payroll_batch_id');
    }
}
