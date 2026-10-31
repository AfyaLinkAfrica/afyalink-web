<?php

namespace App\Support\Assistant;

/**
 * What the assistant found in a message. Each list can hold several candidates;
 * a filter is applied only when exactly one candidate was found.
 */
final class Understanding
{
    /**
     * @param  list<string>  $counties
     * @param  list<string>  $types
     * @param  list<array{slug: string, name: string}>  $services
     */
    public function __construct(
        public readonly array $counties = [],
        public readonly array $types = [],
        public readonly array $services = [],
    ) {}

    /**
     * Search parameters in a fixed order: county, type, service.
     *
     * @return array<string, string>
     */
    public static function query(?string $county, ?string $type, ?string $service): array
    {
        return array_filter([
            'county' => $county,
            'type' => $type,
            'service' => $service,
        ], fn (?string $value) => $value !== null);
    }

    public function isEmpty(): bool
    {
        return $this->counties === [] && $this->types === [] && $this->services === [];
    }

    public function county(): ?string
    {
        return count($this->counties) === 1 ? $this->counties[0] : null;
    }

    public function type(): ?string
    {
        return count($this->types) === 1 ? $this->types[0] : null;
    }

    /**
     * @return array{slug: string, name: string}|null
     */
    public function service(): ?array
    {
        return count($this->services) === 1 ? $this->services[0] : null;
    }

    /**
     * The search parameters for the parts that were clear.
     *
     * @return array<string, string>
     */
    public function filters(): array
    {
        return self::query($this->county(), $this->type(), $this->service()['slug'] ?? null);
    }
}
