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
}
