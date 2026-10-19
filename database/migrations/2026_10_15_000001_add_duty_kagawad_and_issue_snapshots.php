<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Duty Kagawad: signs in place of the Punong Barangay when he or she is away
        Schema::table('barangay_settings', function (Blueprint $table) {
            $table->boolean('duty_kagawad_active')->default(false);
            $table->string('duty_kagawad_name')->nullable();
            $table->string('duty_kagawad_title')->default('Barangay Kagawad (On Duty)');
        });

        // Remember who signed / witnessed each certificate at the time it was issued,
        // so a reprint from History looks exactly like the original.
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('duty_kagawad_name')->nullable();
            $table->string('duty_kagawad_title')->nullable();
            $table->string('witness_name')->nullable();
            $table->string('witness_title')->nullable();
        });

        // Fix saved (customised) certificate texts that still contain fixed "her / she / of legal age"
        $fixes = [
            'jobseeker' => [
                ['informed of her rights', 'informed of {his_her} rights'],
                ['Oath of Undertaking she has signed', 'Oath of Undertaking {he_she} has signed'],
            ],
            'residency' => [
                [', of legal age,', ', {legal_age},'],
            ],
        ];
        foreach ($fixes as $type => $pairs) {
            $row = DB::table('certificate_templates')->where('document_type', $type)->first();
            if (! $row) {
                continue;
            }
            $body = $row->body_content;
            foreach ($pairs as [$from, $to]) {
                $body = str_replace($from, $to, $body);
            }
            if ($body !== $row->body_content) {
                DB::table('certificate_templates')->where('id', $row->id)->update(['body_content' => $body]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['duty_kagawad_name', 'duty_kagawad_title', 'witness_name', 'witness_title']);
        });
        Schema::table('barangay_settings', function (Blueprint $table) {
            $table->dropColumn(['duty_kagawad_active', 'duty_kagawad_name', 'duty_kagawad_title']);
        });
    }
};
