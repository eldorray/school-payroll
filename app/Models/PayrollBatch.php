<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollBatch extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * Get formatted month name
     */
    public function getMonthNameAttribute()
    {
        return date('F', mktime(0, 0, 0, $this->month, 10));
    }

    /**
     * Get formatted period (e.g. "Januari 2026")
     */
    public function getPeriodAttribute()
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return ($months[$this->month] ?? '') . ' ' . $this->year;
    }

    /**
     * Get display name (name if set, otherwise period + created time)
     */
    public function getDisplayNameAttribute()
    {
        if ($this->name) {
            return $this->name;
        }
        return $this->period . ' (#' . $this->id . ')';
    }

    /**
     * Get total amount for this batch
     */
    public function getTotalAmountAttribute()
    {
        return $this->payrolls()->sum('total_salary');
    }
}
