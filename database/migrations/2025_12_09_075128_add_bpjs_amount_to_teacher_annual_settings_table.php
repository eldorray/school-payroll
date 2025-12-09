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
        Schema::table('teacher_annual_settings', function (Blueprint $table) {
            $table->decimal('bpjs_amount', 15, 2)->default(0)->after('teaching_hours_per_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_annual_settings', function (Blueprint $table) {
            $table->dropColumn('bpjs_amount');
        });
    }
};
