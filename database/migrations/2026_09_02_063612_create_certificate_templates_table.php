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
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            // e.g., 'clearance', 'indigency', 'residency', 'jobseeker'
            $table->string('document_type')->unique(); 
            
            // The text that goes inside your green CSS ribbon
            $table->string('title'); 
            
            // e.g., 'TO WHOM IT MAY CONCERN:'
            $table->string('salutation')->default('TO WHOM IT MAY CONCERN:'); 
            
            // The main paragraph(s) containing the {shortcodes}
            $table->text('body_content'); 
            
            // e.g., 'HON. JOVENCIO P. EGIPTO'
            $table->string('signatory_name'); 
            
            // e.g., 'Punong Barangay'
            $table->string('signatory_title')->default('Punong Barangay'); 
            
            // e.g., 'siemprevivasurbarangay@gmail.com'
            $table->string('contact_email')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
