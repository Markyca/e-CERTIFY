<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangay_settings', function (Blueprint $table) {
            // Barangay identity, used everywhere a name used to be hard-coded
            $table->string('barangay_name', 100)->default('Siempre Viva Sur');
            $table->string('municipality', 100)->default('Mallig');
            $table->string('province', 100)->default('Isabela');

            // Default "Witnessed by" on the First Time Job Seeker certificate
            $table->string('witness_name')->default('ROSEMARIE M. GALANGAM');
            $table->string('witness_title')->default('Barangay Secretary');
        });
    }

    public function down(): void
    {
        Schema::table('barangay_settings', function (Blueprint $table) {
            $table->dropColumn(['barangay_name', 'municipality', 'province', 'witness_name', 'witness_title']);
        });
    }
};
