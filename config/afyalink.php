<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Counties of Kenya
    |--------------------------------------------------------------------------
    |
    | The 47 counties, used by the home page search and the facility filters.
    | Facility records store the county name exactly as written here.
    |
    */

    'counties' => [
        'Mombasa', 'Kwale', 'Kilifi', 'Tana River', 'Lamu', 'Taita-Taveta',
        'Garissa', 'Wajir', 'Mandera', 'Marsabit', 'Isiolo', 'Meru',
        'Tharaka-Nithi', 'Embu', 'Kitui', 'Machakos', 'Makueni', 'Nyandarua',
        'Nyeri', 'Kirinyaga', "Murang'a", 'Kiambu', 'Turkana', 'West Pokot',
        'Samburu', 'Trans Nzoia', 'Uasin Gishu', 'Elgeyo-Marakwet', 'Nandi',
        'Baringo', 'Laikipia', 'Nakuru', 'Narok', 'Kajiado', 'Kericho',
        'Bomet', 'Kakamega', 'Vihiga', 'Bungoma', 'Busia', 'Siaya', 'Kisumu',
        'Homa Bay', 'Migori', 'Kisii', 'Nyamira', 'Nairobi',
    ],

    /*
    | Counties shown as quick links on the home page.
    */

    'featured_counties' => [
        'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Machakos', 'Kiambu',
    ],

    /*
    |--------------------------------------------------------------------------
    | Interface languages
    |--------------------------------------------------------------------------
    |
    | The only locales a visitor can choose, as code => name written in that language.
    | Each code needs a matching lang/<code> folder. Anything not listed here is rejected.
    |
    | Kiswahili is off until a fluent healthcare reviewer has approved the lines marked
    | "healthcare-review" in lang/sw. Set AFYALINK_ENABLE_SWAHILI=true to turn it on.
    | With one language enabled the language switcher is not shown.
    |
    */

    'locales' => array_filter([
        'en' => 'English',
        'sw' => env('AFYALINK_ENABLE_SWAHILI', false) ? 'Kiswahili' : null,
    ]),

];
