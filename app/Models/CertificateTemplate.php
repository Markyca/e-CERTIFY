<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type',
        'title',
        'salutation',
        'body_content',
        'signatory_name',
        'signatory_title',
        'contact_email',
        'lgu_logo',          // ADD THIS
        'brgy_logo',         // ADD THIS
        'captain_signature', // ADD THIS
    ];
}