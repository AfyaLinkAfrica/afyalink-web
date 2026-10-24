<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    public const TYPE_HOSPITAL = 'hospital';

    public const TYPE_HEALTH_CENTRE = 'health_centre';

    public const TYPE_DISPENSARY = 'dispensary';

    public const TYPE_CLINIC = 'clinic';

    public const TYPES = [
        self::TYPE_HOSPITAL,
        self::TYPE_HEALTH_CENTRE,
        self::TYPE_DISPENSARY,
        self::TYPE_CLINIC,
    ];

    protected $fillable = [
        'name',
        'type',
        'county',
        'sub_county',
        'address',
        'phone',
        'latitude',
        'longitude',
        'source',
        'verified_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'verified_at' => 'datetime',
    ];

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }
}
