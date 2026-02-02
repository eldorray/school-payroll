<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use App\Models\PayrollSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add nullable unit_id column first
        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('academic_year_id')->constrained()->cascadeOnDelete();
        });

        // Step 2: Duplicate existing settings for each unit
        $existingSettings = DB::table('payroll_settings')->whereNull('unit_id')->get();
        $units = Unit::all();

        foreach ($existingSettings as $setting) {
            foreach ($units as $index => $unit) {
                if ($index === 0) {
                    // Update the first unit to the existing record
                    DB::table('payroll_settings')
                        ->where('id', $setting->id)
                        ->update(['unit_id' => $unit->id]);
                } else {
                    // Create new records for other units
                    DB::table('payroll_settings')->insert([
                        'academic_year_id' => $setting->academic_year_id,
                        'unit_id' => $unit->id,
                        'teaching_rate_per_hour' => $setting->teaching_rate_per_hour,
                        'transport_rate_per_visit' => $setting->transport_rate_per_visit,
                        'masa_kerja_rate_per_year' => $setting->masa_kerja_rate_per_year ?? 0,
                        'late_deduction_rate' => $setting->late_deduction_rate ?? 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Step 3: Make unit_id required and add unique constraint
        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->unique(['academic_year_id', 'unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->dropUnique(['academic_year_id', 'unit_id']);
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });
    }
};
