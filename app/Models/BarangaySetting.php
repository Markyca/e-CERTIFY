<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangaySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'captain_name',
        'captain_title',
        'show_signature',
        'lgu_logo',
        'brgy_logo',
        'qr_code',
        'captain_signature',
    ];
}