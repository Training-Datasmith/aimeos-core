<?php

declare(strict_types=1);

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2026
 */

return [
    'common' => [
        'media' => [
            'mimeicon' => [
                'directory' => dirname(__DIR__) . '/tmp/media/mimeicons',
            ],
            'previews' => [
                0 => [
                    'maxwidth' => 32,
                ],
                1 => [
                    'maxwidth' => 50,
                ],
            ],
        ],
    ],
];
