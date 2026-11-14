<?php

// Kiswahili. Lines marked "healthcare-review" need approval from a fluent healthcare reviewer.
// The helper only understands English words for now, so the examples and the placeholder stay in English.
return [
    'title' => 'Tafuta kituo kwa kuuliza', // healthcare-review
    'intro' => 'Andika unachotafuta kwa maneno yako mwenyewe. Hii itakugeuzia maneno hayo kuwa utafutaji.',
    'limits' => 'Hii husaidia kutafuta vituo tu. Haiwezi kutoa ushauri wa kitabibu, kutambua ugonjwa wala kupendekeza matibabu. Unachoandika hakihifadhiwi. Kwa sasa inaelewa maneno ya Kiingereza pekee.', // healthcare-review
    'label' => 'Unatafuta nini?',
    'placeholder' => 'Kwa mfano: child immunisation in Kisumu',
    'button' => 'Tafuta vituo', // healthcare-review

    'examples_title' => 'Mifano (andika kwa Kiingereza)',
    'examples' => [
        'Child immunisation in Kisumu',
        'HIV testing at a health centre in Mombasa',
        'A pharmacy in Nakuru',
        'Maternity and delivery at a hospital in Kakamega',
    ],

    'understood_title' => 'Hiki ndicho nilichoelewa',
    'show' => 'Onyesha vituo hivi', // healthcare-review
    'not_understood' => 'Sikupata kaunti, aina ya kituo wala huduma katika ulichoandika. Jaribu kutaja mahali na unachohitaji, au tumia utafutaji kamili.', // healthcare-review
    'full_search' => 'Tumia utafutaji kamili',

    'choose_county' => 'Nimepata zaidi ya kaunti moja. Unamaanisha ipi?',
    'choose_type' => 'Nimepata zaidi ya aina moja ya kituo. Unamaanisha ipi?', // healthcare-review
    'choose_service' => 'Nimepata zaidi ya huduma moja. Unamaanisha ipi?', // healthcare-review

    'emergency' => 'Huenda hii ni dharura. Usisubiri: wasiliana na huduma za dharura za eneo lako au nenda kwenye kituo cha afya kilicho karibu sasa hivi. Ukurasa huu hutafuta orodha za vituo pekee na hauwezi kusaidia wakati wa dharura.', // healthcare-review

    'error_empty' => 'Tafadhali andika unachotafuta.',
    'error_too_long' => 'Maneno ni mengi mno. Tafadhali tumia herufi 300 au chache zaidi.',
];
