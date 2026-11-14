<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
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

    /**
     * Services in alphabetical order of the name shown to visitors, which is not the stored order
     * once names are translated. The collections are small, so this is done after fetching.
     *
     * @param  Collection<int, Service>  $services
     * @return Collection<int, Service>
     */
    public static function sortedByLabel(Collection $services): Collection
    {
        return $services->sortBy(fn (Service $service) => mb_strtolower($service->label))->values();
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
