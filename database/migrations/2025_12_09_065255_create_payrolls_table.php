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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            
            $table->integer('month'); // 1-12
            $table->integer('year');  // e.g. 2025
            
            $table->integer('teaching_hours')->default(0);
            $table->integer('attendance_days')->default(0);
            
            $table->decimal('total_salary', 15, 2)->default(0);
            $table->json('details')->nullable(); // Snapshot of rates/allowances used
            
            $table->timestamps();

            // Prevent duplicate payrolls for same teacher in same period
            $table->unique(['academic_year_id', 'teacher_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
