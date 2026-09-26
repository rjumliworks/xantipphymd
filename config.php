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

    // The playful 3-screen gate before everything (gate.php). Replay: /gate.php
    'gate' => [
        'years'        => 4,
        'wife'         => 'Xantipphy',
        'husband'      => 'Ra-ouf',
        'husband_nick' => 'KRAD',
        'date'         => '[Anniversary date]',

        // The celebration when she clicks "No" (not angry anymore).
        'no_line'      => 'Alam ko hindi mo ako matiis! 🥰',

        // The card that pops up after she submits the wishlist.
        'wish_note'    => 'Pannal ko ba Xantipphy misan ako miskin malasa kaw? Or lasa ko lang sapat na?',
        'wish_answers' => ['Your love is enough 💕', 'Both, please 😌'],

        // ── Step 4: "Gift unlocked" — the scrapbook + your personal message ──────────
        // Photos: drop them in assets/img/us/ named in order (01.jpg, 02.jpg, …).
        // Big phone photos are fine — resized copies are made automatically.
        // Captions are matched by filename; photos without one just show no caption.
        'gift_title'    => 'Gift unlocked! 🎁',
        'gift_subtitle' => '4 years of us',
        // Suggested captions — edit freely (add a place or year if you like).
        'captions'      => [
            '01.jpg' => 'My forever plus-one ✨',
            '02.jpg' => 'The day you became my wife 💍',
            '03.jpg' => 'Forever starts here',
            '04.jpg' => 'Still looking at you like this',
            '05.jpg' => 'My favorite place — right beside you',
            '06.jpg' => 'That laugh 🥹',
            '07.jpg' => 'Running into forever with you',
            '08.jpg' => 'Us, facing everything together',
            '09.jpg' => 'Fries, juice, and you 🍹',
            '10.jpg' => 'Every step, holding your hand',
            '11.jpg' => 'The way you smile at me',
            '12.jpg' => 'Your head on my shoulder, always',
            '13.jpg' => 'Walking the shore with you 🌊',
            '14.jpg' => 'Holding hands, even on the swings',
            '15.jpg' => 'So proud of you 🎓',
            '16.jpg' => 'Home is wherever you are 🌙',
            '17.jpg' => 'Adventures with my favorite person',
            '18.jpg' => 'Just us and the big blue sea',
        ],
        // Your personal message, one paragraph per line, used exactly as written.
        'message_title' => 'A message from your KRAD',
        'message'       => [
            '[Your personal message to her — write it however you like: English, Tagalog, or your own language.]',
            '[Second paragraph.]',
            '[A last line.]',
        ],
        'message_sign'  => 'Forever yours, Ra-ouf (KRAD)',

        // ── The final question (after the letter). Answer is case-insensitive. ─────────
        'quiz_question' => 'What is your favorite thing to do with me?',
        'quiz_answer'   => 'bembang',
        // Shown after 3, 6 and 9 wrong answers.
        'quiz_hints'    => [
            'Hint: it starts with “B” and has 7 letters 😏',
            'Hint: B E M _ _ _ G 🙈',
            'Okay fine: b-e-m-b-a-n-… you know the last letter 🤭',
        ],
        'quiz_correct'  => 'HAHAHA I knew it! 🙈😏',

        // ── "Evidence" photos clipped to NDA clauses (clause number => photo) ────────
        // Add the files to show them; missing ones show a small placeholder.
        'exhibits' => [
            1 => ['file' => 'assets/img/exhibit-a.jpg', 'label' => 'Exhibit A', 'caption' => 'Snoring, 2:13 a.m.'],
            6 => ['file' => 'assets/img/exhibit-b.jpg', 'label' => 'Exhibit B', 'caption' => 'Caught with the last slice'],
            7 => ['file' => 'assets/img/exhibit-c.jpg', 'label' => 'Exhibit C', 'caption' => 'Patient: very dramatic'],
        ],

        // Your face for the reactions. A square-ish head-and-shoulders photo works best
        // (PNG with the background removed is even funnier). Until it exists, a 😭 stands in.
        // Optional per-gift photos override it: assets/img/me-jewelry.png, me-land.png,
        // me-car.png, me-money.png, me-happy.png (relieved/smiling), me-faint.png.
        'me_photo' => 'assets/img/me.png',
        // Where the tears start on your photo, as % of its width/height (left eye, right eye).
        'eyes'     => [[38, 44], [62, 44]],
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
        'kicker' => 'Internal Medicine · [City]',
        // DRAFT
        'lede'   => 'Careful, unhurried medicine for adults — from a first question to a condition you have lived with for years.',
        // Best as a transparent cut-out PNG (subject on no background), ~1400px tall.
        'image'  => 'assets/img/hero.png',
    ],

    // "At a glance" strip under the hero — real facts only, no invented numbers.
    'glance' => [
        'Specialty'     => 'Internal Medicine',
        'Trained at'    => '[Medical school]',
        'Certification' => '[Board certification]',
        'Consults in'   => '[Languages]',
    ],

    'about' => [
        'heading' => 'Medicine begins with <em>listening.</em>',
        'bio' => [
            '[Biography, paragraph one — where she trained, and what drew her to internal medicine. Best written in her own voice.]',
            '[Paragraph two — where she practises today, and who she cares for.]',
        ],
        'image'     => 'assets/img/about.jpg',
        'image_alt' => 'Dr. Xantipphy [Surname]',
    ],

    'care' => [
        // DRAFT — a general, non-clinical description of the specialty
        'intro' => 'Internal medicine is the care of adults as whole people — looking at how things connect, rather than one part at a time.',
        // DRAFT — confirm which services she actually offers. Icon keys: see inc/icons.php
        'items' => [
            ['shield',      'Preventive Care',          'Check-ups, age-appropriate screening and a clear plan for staying well.'],
            ['stethoscope', 'Adult Primary Care',       'A steady first point of contact for new symptoms and ongoing questions.'],
            ['pulse',       'Chronic Conditions',       'Long-term care for blood pressure, diabetes, cholesterol and more.'],
            ['clipboard',   'Health Assessment',        'A thorough review of history, medications and results in one place.'],
            ['chat',        'General Concerns',         'The symptom that lingers, or the result you didn’t quite understand.'],
            ['leaf',        'Lifestyle & Wellness',     'Realistic guidance on sleep, nutrition, movement and stress.'],
        ],
    ],

    'consultation' => [
        'status' => 'Opening [Month Year]',
        // DRAFT
        'body'   => 'A short, unhurried conversation about a health concern, a result you would like explained, or whether you should be seen in person.',
        'steps'  => [
            ['Send a short request', 'A few lines about what is on your mind. No medical records needed.'],
            ['Receive a reply',      'A personal response to arrange a time that suits you.'],
            ['Talk it through',      'A conversation, and an honest view of what should happen next.'],
        ],
        'image'     => 'assets/img/consult.jpg',
        'image_alt' => '[Describe the photograph]',
        // Review before production
        'disclaimer' => 'This service is intended for general health guidance and does not replace emergency medical care, physical examination, diagnosis, or treatment when in-person evaluation is required. If you are experiencing a medical emergency, call your local emergency number immediately.',
    ],

    'letter' => [
        'label'      => 'A personal note',
        'title'      => 'Behind the physician',
        'salutation' => 'Xantipphy,',
        'body'       => [
            '[Your letter to her. A few short paragraphs read best here.]',
            '[Second paragraph.]',
        ],
        'closing'    => 'With all my love,',
        'signature'  => 'Ra-ouf — your KRAD',
        'date'       => '[Anniversary date]',
        // A cut-out PNG looks best here too, but a normal photo works.
        'image'      => 'assets/img/together.png',
        'image_alt'  => '[Describe the photograph of the two of you]',
    ],

    // SAMPLE CONTENT — replace with real articles. Set 'url' to link a card; 'image' is optional.
    'notes' => [
        ['title' => 'What to bring to your first appointment',  'category' => 'Practical',     'image' => 'assets/img/note-1.jpg', 'url' => null],
        ['title' => 'How to prepare questions for your doctor', 'category' => 'Practical',     'image' => 'assets/img/note-2.jpg', 'url' => null],
        ['title' => 'Reading your routine blood test report',   'category' => 'Understanding', 'image' => 'assets/img/note-3.jpg', 'url' => null],
        ['title' => 'Keeping an up-to-date medication list',    'category' => 'Everyday',      'image' => 'assets/img/note-4.jpg', 'url' => null],
    ],
    'notes_summary' => 'Placeholder summary — replace with one or two sentences about the article.',

    'contact' => [
        'email'   => '[email@domain.com]',
        'phone'   => '[Phone]',
        'clinic'  => '[Clinic name]',
        'address' => '[Street], [City], [Region]',
        'hours'   => '[Consultation hours]',
        'image'   => 'assets/img/contact.png',
        'social'  => [
            'LinkedIn'  => '[URL]',
            'Instagram' => '[URL]',
        ],
    ],

    'legal' => [
        'privacy' => '[Privacy policy — to be written before launch. This preview stores only your theme preference in your browser and sends no form data anywhere.]',
    ],
];
