<?php

declare(strict_types=1);

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */

return [
    'manager' => [
        'currency' => [
            'delete' => [
                'ansi' => '
					DELETE FROM "mshop_locale_currency" WHERE :cond
				',
            ],
            'insert' => [
                'ansi' => '
					INSERT INTO "mshop_locale_currency" ( :names
						"label", "status", "mtime", "editor", "id", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?
					)
				',
            ],
            'update' => [
                'ansi' => '
					UPDATE "mshop_locale_currency"
					SET :names
						"label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "id" = ?
				',
            ],
        ],
        'language' => [
            'delete' => [
                'ansi' => '
					DELETE FROM "mshop_locale_language" WHERE :cond
				',
            ],
            'insert' => [
                'ansi' => '
					INSERT INTO "mshop_locale_language" ( :names
						"label", "status", "mtime", "editor", "id", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?
					)
				',
            ],
            'update' => [
                'ansi' => '
					UPDATE "mshop_locale_language"
					SET :names
						"label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "id" = ?
				',
            ],
        ],
        'site' => [
            'cleanup' => [
                'shop' => [
                    'domains' => [
                        'attribute' => 'attribute',
                        'basket' => 'basket',
                        'catalog' => 'catalog',
                        'coupon' => 'coupon',
                        'customer' => 'customer',
                        'group' => 'group',
                        'index' => 'index',
                        'media' => 'media',
                        'order' => 'order',
                        'plugin' => 'plugin',
                        'price' => 'price',
                        'product' => 'product',
                        'review' => 'review',
                        'rule' => 'rule',
                        'tag' => 'tag',
                        'service' => 'service',
                        'stock' => 'stock',
                        'subscription' => 'subscription',
                        'supplier' => 'supplier',
                        'text' => 'text',
                        'type' => 'type',
                    ],
                ],
                'admin' => [
                    'domains' => [
                        'job' => 'job',
                        'log' => 'log',
                        'cache' => 'cache',
                    ],
                ],
            ],
            'delete' => [
                'ansi' => '
					DELETE FROM "mshop_locale_site"
					WHERE :cond
				',
            ],
            'insert' => [
                'ansi' => '
					INSERT INTO "mshop_locale_site" ( :names
						"siteid", "code", "label", "config", "status", "icon", "logo",
						"refid", "theme", "editor", "mtime", "ctime", "parentid", "level",
						"nleft", "nright"

					)
					SELECT :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0,
						COALESCE( MAX("nright"), 0 ) + 1, COALESCE( MAX("nright"), 0 ) + 2
					FROM "mshop_locale_site"
				',
            ],
            'rate' => [
                'ansi' => '
					UPDATE "mshop_locale_site"
					SET "rating" = ?, "ratings" = ?
					WHERE "id" = ?
				',
            ],
            'update' => [
                'ansi' => '
					UPDATE "mshop_locale_site"
					SET :names
						"siteid" = ?, "code" = ?, "label" = ?, "config" = ?, "status" = ?,
						"icon" = ?, "logo" = ?, "refid" = ?, "theme" = ?, "editor" = ?, "mtime" = ?
					WHERE id = ?
				',
            ],
        ],
        'delete' => [
            'ansi' => '
				DELETE FROM "mshop_locale"
				WHERE :cond AND "siteid" LIKE ?
			',
        ],
        'insert' => [
            'ansi' => '
				INSERT INTO "mshop_locale" ( :names
					"langid", "currencyid", "pos", "status",
					"mtime", "editor", "site_id", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			',
        ],
        'update' => [
            'ansi' => '
				UPDATE "mshop_locale"
				SET :names
					"langid" = ?, "currencyid" = ?, "pos" = ?,
					"status" = ?, "mtime" = ?, "editor" = ?, "site_id" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			',
        ],
        'search' => [
            'ansi' => '
				SELECT :columns, mlocsi."code" AS "locale.sitecode"
				FROM "mshop_locale" mloc
				LEFT JOIN "mshop_locale_site" mlocsi ON (mloc."site_id" = mlocsi."id")
				LEFT JOIN "mshop_locale_language" mlocla ON (mloc."langid" = mlocla."id")
				LEFT JOIN "mshop_locale_currency" mloccu ON (mloc."currencyid" = mloccu."id")
				WHERE :cond
				GROUP BY :group, mlocsi."code"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
            'mysql' => '
				SELECT :columns, mlocsi."code" AS "locale.sitecode"
				FROM "mshop_locale" mloc
				LEFT JOIN "mshop_locale_site" mlocsi ON (mloc."site_id" = mlocsi."id")
				LEFT JOIN "mshop_locale_language" mlocla ON (mloc."langid" = mlocla."id")
				LEFT JOIN "mshop_locale_currency" mloccu ON (mloc."currencyid" = mloccu."id")
				WHERE :cond
				GROUP BY :group
				ORDER BY :order
				LIMIT :size OFFSET :start
			',
        ],
        'count' => [
            'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mloc."id"
					FROM "mshop_locale" mloc
					LEFT JOIN "mshop_locale_site" mlocsi ON (mloc."site_id" = mlocsi."id")
					LEFT JOIN "mshop_locale_language" mlocla ON (mloc."langid" = mlocla."id")
					LEFT JOIN "mshop_locale_currency" mloccu ON (mloc."currencyid" = mloccu."id")
					WHERE :cond
					GROUP BY mloc."id"
					ORDER BY mloc."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
            'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mloc."id"
					FROM "mshop_locale" mloc
					LEFT JOIN "mshop_locale_site" mlocsi ON (mloc."site_id" = mlocsi."id")
					LEFT JOIN "mshop_locale_language" mlocla ON (mloc."langid" = mlocla."id")
					LEFT JOIN "mshop_locale_currency" mloccu ON (mloc."currencyid" = mloccu."id")
					WHERE :cond
					GROUP BY mloc."id"
					ORDER BY mloc."id"
					LIMIT 10000 OFFSET 0
				) AS list
			',
        ],
        'submanagers' => [
            'currency' => 'currency',
            'language' => 'language',
            'site' => 'site',
        ],
    ],
];
