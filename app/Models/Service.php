<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Lang;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * The name shown to visitors: the translation for this slug in the current language,
     * or the stored name for services that have none (for example ones added by an import).
     */
    public static function label(string $slug, string $name): string
    {
        return Lang::has('services.'.$slug) ? __('services.'.$slug) : $name;
    }

    public function getLabelAttribute(): string
    {
        return self::label($this->slug, $this->name);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class);
    }
}
