<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Limit results to one county. A null value leaves the query unchanged.
     */
    public function scopeInCounty(Builder $query, ?string $county): Builder
    {
        return $county === null ? $query : $query->where('county', $county);
    }

    /**
     * Limit results to one facility type. A null value leaves the query unchanged.
     */
    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $type === null ? $query : $query->where('type', $type);
    }

    /**
     * Match part of a facility name. A null or empty term leaves the query unchanged.
     * The LIKE wildcards (% and _) are removed so visitors cannot widen the search.
     */
    public function scopeNameLike(Builder $query, ?string $term): Builder
    {
        $term = trim(str_replace(['%', '_'], ' ', (string) $term));

        return $term === '' ? $query : $query->where('name', 'like', '%'.$term.'%');
    }

    /**
     * Limit results to facilities offering one service (matched by slug).
     * A null value leaves the query unchanged.
     */
    public function scopeOfferingService(Builder $query, ?string $slug): Builder
    {
        return $slug === null
            ? $query
            : $query->whereHas('services', fn (Builder $services) => $services->where('slug', $slug));
    }
}
