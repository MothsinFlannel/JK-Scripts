<?php

use vr\environment\Environment;
use vr\environment\InlineFlavor;
use vr\environment\JsonFlavor;

/** @noinspection SpellCheckingInspection */

return [
    'class' => Environment::class,
    'default' => 'local',
    'flavors' => [
        'local' => [
            'class' => JsonFlavor::class,
            'filename' => '@app/flavors/local.flavor.json'
        ],
        'develop' => [
            'class' => InlineFlavor::class,
            'components' => [
                'db' => [
                    'dsn' => 'pgsql:host=localhost;dbname=vcard',
                    'username' => 'postgres',
                    'password' => 'password',
                    'charset' => 'utf8',
                ]
            ],
            'params' => [
                'emails' => [
                    'service' => [
                        ['email' => 'a.kryshtalev@voodoo.rocks'],
                        ['email' => 'e.bodomolova@voodoo.rocks']
                    ]
                ]
            ],
        ],
        'staging' => [
            'class' => InlineFlavor::class,
            'components' => [
                'db' => [
                    'dsn' => 'pgsql:host=localhost;dbname=vcard',
                    'username' => 'vcard',
                    'password' => 'npd7MqRCSUVUwc27',
                    'charset' => 'utf8',
                ],
            ],
            'params' => [
                'appBaseUrl' => 'https://vcard.voodoo.services/',
                'emails' => [
                    'service' => [
                        ['email' => 'a.kryshtalev@voodoo.rocks'],
                        ['email' => 'e.bodomolova@voodoo.rocks']
                    ]
                ]
            ]
        ],
        'production' => [
            'class' => JsonFlavor::class,
            'filename' => '@app/flavors/production.flavor.json'
        ],
    ],
];