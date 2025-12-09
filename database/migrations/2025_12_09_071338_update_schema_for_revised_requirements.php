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
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('position')->nullable()->after('name'); // Jabatan
        });

        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->decimal('masa_kerja_rate_per_year', 15, 2)->default(0)->after('transport_rate_per_visit');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            // Deductions
            $table->decimal('bpjs_amount', 15, 2)->default(0)->after('total_salary');
            $table->decimal('transport_allowance_deduction_amount', 15, 2)->default(0)->after('bpjs_amount'); // e.g. "Terlambat" affects transport mostly, but here it's "Terlambat" column
            $table->decimal('incentive_deduction_amount', 15, 2)->default(0)->after('transport_allowance_deduction_amount'); // "Insentif" deduction
            $table->decimal('other_deduction_amount', 15, 2)->default(0)->after('incentive_deduction_amount'); // "Jml" column if generic
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('position');
        });

        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->dropColumn('masa_kerja_rate_per_year');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'bpjs_amount',
                'transport_allowance_deduction_amount',
                'incentive_deduction_amount',
                'other_deduction_amount'
            ]);
        });
    }
};
