<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, we need to drop the foreign key, then the unique index, then recreate the foreign key
        Schema::table('payrolls', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['teacher_id']);
        });
        
        Schema::table('payrolls', function (Blueprint $table) {
            // Now drop the unique constraint
            $table->dropUnique('payrolls_academic_year_id_teacher_id_month_year_unique');
        });
        
        Schema::table('payrolls', function (Blueprint $table) {
            // Recreate foreign keys
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->foreign('teacher_id')->references('id')->on('teachers')->cascadeOnDelete();
            
            // Add a new unique constraint that includes payroll_batch_id
            // This allows multiple entries for same teacher/month/year as long as they're in different batches
            $table->unique(['payroll_batch_id', 'teacher_id'], 'payrolls_batch_teacher_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropUnique('payrolls_batch_teacher_unique');
            $table->unique(['academic_year_id', 'teacher_id', 'month', 'year']);
        });
    }
};
