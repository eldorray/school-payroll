<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->string('location')->nullable()->after('code'); // e.g., "Cirebon"
            $table->string('principal_name')->nullable()->after('location'); // Nama Kepala Sekolah
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['location', 'principal_name']);
        });
    }
};
