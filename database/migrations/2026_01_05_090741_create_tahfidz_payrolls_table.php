<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tahfidz_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            
            // Jabatan (Koordinator Tahfidz, Guru Tahfidz)
            $table->string('jabatan')->nullable();
            
            // Jam (JML JAM x rate)
            $table->integer('jml_jam')->default(0);
            $table->decimal('jam_rate', 12, 2)->default(15000);
            $table->decimal('jumlah_jam', 15, 2)->default(0);
            
            // Hari (JUMLAH HARI x transport rate)
            $table->integer('jml_hari')->default(0);
            $table->decimal('transport_rate', 12, 2)->default(16000);
            $table->decimal('jumlah_transport', 15, 2)->default(0);
            
            // Masa Kerja (JM MK x rate)
            $table->integer('jm_mk')->default(0);
            $table->decimal('mk_rate', 12, 2)->default(8000);
            $table->decimal('jumlah_mk', 15, 2)->default(0);
            
            // Tunjangan Jabatan
            $table->decimal('tunjangan_jabatan', 15, 2)->default(0);
            
            // Transport Tambahan
            $table->decimal('transport_tambahan', 15, 2)->default(0);
            
            // Insentif (tambahan)
            $table->decimal('insentif', 15, 2)->default(0);
            
            // Potongan BPJS
            $table->decimal('potongan_bpjs', 15, 2)->default(0);
            
            // Total Penerimaan
            $table->decimal('total', 15, 2)->default(0);
            
            $table->timestamps();
            
            $table->index(['unit_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahfidz_payrolls');
    }
};
