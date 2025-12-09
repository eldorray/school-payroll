<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAnnualSetting extends Model
{
    protected $guarded = [];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
