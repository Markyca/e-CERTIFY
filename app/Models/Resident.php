<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'birth_date',
        'gender',
        'civil_status',
        'purok',
        'is_active',
    ];

    // Relationship: A resident can have multiple certificates
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
    
}