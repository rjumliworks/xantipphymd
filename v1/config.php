<?php
/*
|--------------------------------------------------------------------------
| Site content
|--------------------------------------------------------------------------
| Every word on the page lives here. Text in [BRACKETS] is a placeholder and
| is highlighted on the page until replaced. Entries marked DRAFT are layout
| copy written in a neutral voice — review with her before publishing.
*/

return [

    'person' => [
        'title'      => 'Dr.',
        'first_name' => 'Xantipphy',          // confirm spelling
        'last_name'  => '[Surname]',
        'suffix'     => 'MD',                 // taken from the folder name — confirm
        'role'       => 'Internal Medicine Physician',
        'specialty'  => 'Internal Medicine',
        'city'       => '[City]',
        'region'     => '[Region]',
    ],

    'site' => [
        'url'      => '',                     // e.g. https://drxantipphy.com — used for canonical + OG
        'og_image' => 'assets/img/og.jpg',
    ],

    // Opening letter (first visit only; replay with ?letter)
    'intro' => [
        'lines' => [
            'For the woman who spends her days caring for others.',
            'And the woman who means the world to me.',
        ],
        'name'   => 'Dr. Xantipphy',
        'corner' => 'A letter',
        'date'   => '[Anniversary date]',
    ],

    'hero' => [
        // DRAFT
        'lede'        => 'Careful, unhurried medicine for adults — from a first question to a condition you have lived with for years.',
        'credentials' => ['[Medical school]', '[Residency]', '[Board certification]'],
        'portrait'    => 'assets/img/portrait.jpg',
        'plate'       => '[City], [Year]',
        'availability'=> 'Free consultations from [Month Year]',
    ],

    'about' => [
        'bio' => [
            '[Biography, paragraph one — where she trained, and what drew her to internal medicine. Best written in her own voice.]',
            '[Paragraph two — where she practises today, and who she cares for.]',
            '[Optional paragraph three — something human: the languages she speaks, where she is from, what she does away from medicine.]',
        ],
        'facts' => [
            'Specialty'     => 'Internal Medicine',
            'Medical degree'=> '[Medical school, year]',
            'Residency'     => '[Residency programme]',
            'Certification' => '[Board certification]',
            'Languages'     => '[Languages]',
            'Practice'      => '[Clinic or hospital]',
        ],
    ],

    'care' => [
        // DRAFT — a general, non-clinical description of the specialty
        'intro' => 'Internal medicine is the care of adults as whole people. An internist looks at how things connect — the blood pressure, the sleep, the stress, the medication list — rather than one part at a time.',
        // DRAFT — confirm which services she actually offers
        'items' => [
            ['Preventive Care',          'Check-ups, screening that suits your age and history, and a clear plan for staying well before anything goes wrong.'],
            ['Adult Primary Care',       'A consistent first point of contact for adults: new symptoms, ongoing questions, and knowing when a specialist is needed.'],
            ['Chronic Conditions',       'Long-term care for conditions such as high blood pressure, diabetes and high cholesterol — planned around your life rather than against it.'],
            ['Health Assessment',        'A thorough review of your history, medications and results, so the whole picture is understood in one place.'],
            ['General Medical Concerns', 'The symptom that has lingered, the result you did not understand, the question you were not sure who to ask.'],
            ['Lifestyle & Wellness',     'Practical guidance on sleep, nutrition, movement and stress — small, realistic changes that tend to last.'],
        ],
    ],

    'approach' => [
        // DRAFT
        'statement'  => 'The most useful thing a doctor can give is <em>attention.</em>',
        'lead'       => '[A few sentences, in her words, on what a first appointment with her feels like.]',
        'principles' => [
            'Time'       => 'Appointments that leave room for the question you almost didn’t ask.',
            'Clarity'    => 'Plain explanations of what is happening, what the options are, and why.',
            'Continuity' => 'Care that remembers you — your history, your preferences, your goals.',
        ],
        'image'      => 'assets/img/approach.jpg',
        'image_alt'  => '[Describe the photograph]',
        'caption'    => '[Caption — a place or a moment]',
    ],

    'consultation' => [
        'status' => 'Opening [Month Year]',
        // DRAFT
        'body'   => 'Soon you will be able to request a free initial consultation — a short, unhurried conversation about a health concern, a result you would like explained, or whether you should be seen in person.',
        'steps'  => [
            ['Send a short request', 'A few lines about what is on your mind. No medical records needed.'],
            ['Receive a reply',      'A personal response to arrange a time that suits you.'],
            ['Talk it through',      'A conversation, and an honest view of what should happen next.'],
        ],
        // Review before production
        'disclaimer' => 'This service is intended for general health guidance and does not replace emergency medical care, physical examination, diagnosis, or treatment when in-person evaluation is required. If you are experiencing a medical emergency, call your local emergency number immediately.',
    ],

    // SAMPLE CONTENT — replace with real articles. Set 'url' to link a row.
    'notes' => [
        ['title' => 'What to bring to your first appointment',     'category' => 'Practical',  'date' => '[Date]', 'url' => null],
        ['title' => 'How to prepare questions for your doctor',    'category' => 'Practical',  'date' => '[Date]', 'url' => null],
        ['title' => 'Reading your routine blood test report',      'category' => 'Understanding', 'date' => '[Date]', 'url' => null],
        ['title' => 'Keeping an up-to-date medication list',       'category' => 'Everyday',   'date' => '[Date]', 'url' => null],
    ],
    'notes_summary' => 'Placeholder summary — replace with one or two sentences describing the article.',

    'letter' => [
        'label'      => 'A personal note',
        'title'      => 'Behind the physician',
        'salutation' => 'Xantipphy,',
        'body'       => [
            '[Your letter to her. A few short paragraphs read best here — as if written by hand, then set in type.]',
            '[Second paragraph.]',
            '[A last line.]',
        ],
        'closing'    => 'With all my love,',
        'signature'  => '[Your name]',
        'date'       => '[Anniversary date]',
        'image'      => 'assets/img/together.jpg',
        'image_alt'  => '[Describe the photograph of the two of you]',
        'caption'    => '[A place, a year]',
    ],

    'contact' => [
        'email'   => '[email@domain.com]',
        'phone'   => '[Phone]',
        'clinic'  => '[Clinic name]',
        'address' => '[Street], [City], [Region]',
        'hours'   => '[Consultation hours]',
        'social'  => [
            'LinkedIn'  => '[URL]',
            'Instagram' => '[URL]',
        ],
    ],

    'legal' => [
        'privacy' => '[Privacy policy — to be written before launch. This preview stores only your theme preference in your browser and sends no form data anywhere.]',
    ],
];
