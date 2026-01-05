<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtracurricularPayroll extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function extracurricular()
    {
        return $this->belongsTo(Extracurricular::class);
    }
}
