<?php
return [
    'frontend' => [
        'phi/phinewsletter/service/newsletteraction' => [
            'target' => \Phi\PhiNewsletter\Service\NewsletterAction::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
        ]
    ]
];
