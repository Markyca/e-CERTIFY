<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'certificate_type',
        'purpose',
        'control_number',
        'qr_code_path',
        'issued_by'
    ];

    // Relationship: A certificate belongs to a specific resident
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    // Relationship: A certificate is issued by a specific user (staff/secretary/captain)
    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}