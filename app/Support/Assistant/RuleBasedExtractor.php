<?php

namespace App\Support\Assistant;

use App\Models\Facility;
use App\Models\Service;

/**
 * Finds a county, a facility type, and a service in a message, using plain word lists.
 *
 * Nothing here calls an outside service, and the message is not stored or logged.
 * The result can only ever be values that the search page already accepts.
 */
class RuleBasedExtractor
{
    /** Extra ways people name each service, keyed by service slug. The service's own name always matches too. */
    private const SERVICE_WORDS = [
        'outpatient-care' => ['outpatient', 'out patient', 'general care', 'consultation', 'doctor', 'see a doctor'],
        'maternity-and-delivery' => ['maternity', 'deliver', 'delivery', 'give birth', 'giving birth', 'childbirth', 'labour', 'labor', 'midwife'],
        'antenatal-and-postnatal-care' => ['antenatal', 'postnatal', 'prenatal', 'pregnant', 'pregnancy', 'anc'],
        'child-immunisation' => ['immunisation', 'immunization', 'immunise', 'immunize', 'vaccine', 'vaccines', 'vaccination', 'vaccinations', 'vaccinate', 'jab', 'jabs'],
        'family-planning' => ['contraception', 'contraceptive', 'contraceptives', 'birth control', 'implant', 'iud', 'condom', 'condoms'],
        'hiv-testing-and-counselling' => ['hiv', 'aids', 'vct', 'hiv test', 'hiv testing'],
        'laboratory-services' => ['laboratory', 'lab', 'labs', 'lab test', 'blood test', 'blood tests', 'urine test'],
        'pharmacy' => ['pharmacy', 'pharmacies', 'chemist', 'medicine', 'medicines', 'medication', 'drugs'],
        'dental-care' => ['dental', 'dentist', 'dentists', 'tooth', 'teeth'],
        'eye-care' => ['eye', 'eyes', 'optical', 'optician', 'eyesight', 'glasses', 'vision'],
    ];

    /** Ways people name each facility type, keyed by the value used in the search. */
    private const TYPE_WORDS = [
        'hospital' => ['hospital', 'hospitals'],
        'health_centre' => ['health centre', 'health centres', 'health center', 'health centers'],
        'dispensary' => ['dispensary', 'dispensaries'],
        'clinic' => ['clinic', 'clinics'],
    ];

    public function extract(string $message): Understanding
    {
        $text = TextNormalizer::normalize($message);

        $services = $this->services($text);

        return new Understanding(
            counties: $this->counties($text),
            types: $this->types($text, hasService: $services !== []),
            services: $services,
        );
    }

    /**
     * @return list<string>
     */
    private function counties(string $text): array
    {
        $found = [];

        foreach (config('afyalink.counties') as $county) {
            if (TextNormalizer::contains($text, $county)) {
                $found[] = $county;
            }
        }

        return $found;
    }

    /**
     * "Clinic" is also everyday speech for any place of care ("antenatal clinic"),
     * so it only counts as a facility type when no service was named.
     *
     * @return list<string>
     */
    private function types(string $text, bool $hasService): array
    {
        $found = [];

        foreach (Facility::TYPES as $type) {
            if ($type === 'clinic' && $hasService) {
                continue;
            }

            foreach (self::TYPE_WORDS[$type] ?? [] as $word) {
                if (TextNormalizer::contains($text, $word)) {
                    $found[] = $type;
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * @return list<array{slug: string, name: string}>
     */
    private function services(string $text): array
    {
        $found = [];

        foreach (Service::query()->orderBy('name')->get(['name', 'slug']) as $service) {
            $words = array_merge([$service->name], self::SERVICE_WORDS[$service->slug] ?? []);

            foreach ($words as $word) {
                if (TextNormalizer::contains($text, $word)) {
                    $found[] = ['slug' => $service->slug, 'name' => $service->name];
                    break;
                }
            }
        }

        return $found;
    }
}
