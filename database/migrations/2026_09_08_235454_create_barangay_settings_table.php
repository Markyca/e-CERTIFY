<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangay_settings', function (Blueprint $table) {
            $table->id();
            $table->string('captain_name')->default('HON. JOVENCIO P. EGIPTO');
            $table->string('captain_title')->default('Punong Barangay');
            $table->boolean('show_signature')->default(true); // Toggle for digital signature printing
            $table->string('lgu_logo')->nullable();
            $table->string('brgy_logo')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('captain_signature')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangay_settings');
    }
};