<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahfidzPayroll extends Model
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

    /**
     * Calculate totals before saving
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Calculate jumlah_jam
            $model->jumlah_jam = $model->jml_jam * $model->jam_rate;
            
            // Calculate jumlah_transport
            $model->jumlah_transport = $model->jml_hari * $model->transport_rate;
            
            // Calculate jumlah_mk (masa kerja)
            $model->jumlah_mk = $model->jm_mk * $model->mk_rate;
            
            // Calculate total penerimaan
            $pendapatan = $model->jumlah_jam + $model->jumlah_transport + $model->jumlah_mk 
                        + $model->tunjangan_jabatan + $model->transport_tambahan + $model->insentif;
            $potongan = $model->potongan_bpjs;
            
            $model->total = $pendapatan - $potongan;
        });
    }
}
