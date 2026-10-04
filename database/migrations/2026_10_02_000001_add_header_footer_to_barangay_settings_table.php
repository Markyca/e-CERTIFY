<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangay_settings', function (Blueprint $table) {
            // One header line per row (newline separated)
            $table->string('header_lines', 600)->default("REPUBLIC OF THE PHILIPPINES\nPROVINCE OF ISABELA\nMUNICIPALITY OF MALLIG\nBARANGAY OF SIEMPRE VIVA SUR");
            $table->string('header_office')->default('Office of the Punong Barangay');
            $table->string('footer_label')->default('SCAN FOR MORE INFO. OR EMAIL');
            $table->string('footer_address')->default('Brgy. Siempre Viva Sur, Mallig, Isabela');
            $table->string('footer_email')->nullable()->default('siemprevivasurbarangay@gmail.com');
        });
    }

    public function down(): void
    {
        Schema::table('barangay_settings', function (Blueprint $table) {
            $table->dropColumn(['header_lines', 'header_office', 'footer_label', 'footer_address', 'footer_email']);
        });
    }
};
