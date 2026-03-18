<?php

declare(strict_types=1);

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */

return [
    'manager' => [
        'attribute' => [
            'delete' => [
                'ansi' => '
					DELETE FROM "mshop_index_attribute"
					WHERE :cond AND "siteid" LIKE ?
				',
            ],
            'insert' => [
                'ansi' => '
					INSERT INTO "mshop_index_attribute" (
						"prodid", "artid", "attrid", "listtype", "type", "code",
						"mtime", "siteid"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?
					)
				',
                'pgsql' => '
					INSERT INTO "mshop_index_attribute" (
						"prodid", "artid", "attrid", "listtype", "type", "code",
						"mtime", "siteid"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?
					)
					ON CONFLICT DO NOTHING
				',
            ],
            'search' => [
                'ansi' => '
					SELECT mpro."id" :mincols
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
                'mysql' => '
					SELECT mpro."id" :mincols
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY :order
					LIMIT :size OFFSET :start
				',
            ],
            'count' => [
                'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpro."id"
						FROM "mshop_product" mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY mpro."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
                'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpro."id"
						FROM "mshop_product" mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY mpro."id"
						LIMIT 10000 OFFSET 0
					) AS list
				',
            ],
            'cleanup' => [
                'ansi' => '
					DELETE FROM "mshop_index_attribute"
					WHERE "mtime" < ? AND "siteid" LIKE ?
				',
            ],
            'optimize' => [
                'mysql' => [
                    'OPTIMIZE TABLE "mshop_index_attribute"',
                ],
                'pgsql' => [],
                'sqlsrv' => [],
            ],
        ],
        'catalog' => [
            'delete' => [
                'ansi' => '
					DELETE FROM "mshop_index_catalog"
					WHERE :cond AND "siteid" LIKE ?
				',
            ],
            'insert' => [
                'ansi' => '
					INSERT INTO "mshop_index_catalog" (
						"prodid", "catid", "listtype", "pos",
						"mtime", "siteid"
					) VALUES (
						?, ?, ?, ?, ?, ?
					)
				',
                'pgsql' => '
					INSERT INTO "mshop_index_catalog" (
						"prodid", "catid", "listtype", "pos",
						"mtime", "siteid"
					) VALUES (
						?, ?, ?, ?, ?, ?
					)
					ON CONFLICT DO NOTHING
				',
            ],
            'search' => [
                'ansi' => '
					SELECT mpro."id" :mincols
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
                'mysql' => '
					SELECT mpro."id" :mincols
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY :order
					LIMIT :size OFFSET :start
				',
            ],
            'count' => [
                'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpro."id"
						FROM "mshop_product" mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY mpro."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
                'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpro."id"
						FROM "mshop_product" mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY mpro."id"
						LIMIT 10000 OFFSET 0
					) AS list
				',
            ],
            'cleanup' => [
                'ansi' => '
					DELETE FROM "mshop_index_catalog"
					WHERE "mtime" < ? AND "siteid" LIKE ?
				',
            ],
            'optimize' => [
                'mysql' => [
                    'OPTIMIZE TABLE "mshop_index_catalog"',
                ],
                'pgsql' => [],
                'sqlsrv' => [],
            ],
        ],
        'price' => [
            'delete' => [
                'ansi' => '
					DELETE FROM "mshop_index_price"
					WHERE :cond AND "siteid" LIKE ?
				',
            ],
            'insert' => [
                'ansi' => '
					INSERT INTO "mshop_index_price" (
						"prodid", "currencyid", "value", "mtime", "siteid"
					) VALUES (
						?, ?, ?, ?, ?
					)
				',
                'pgsql' => '
					INSERT INTO "mshop_index_price" (
						"prodid", "currencyid", "value", "mtime", "siteid"
					) VALUES (
						?, ?, ?, ?, ?
					)
					ON CONFLICT DO NOTHING
				',
            ],
            'search' => [
                'ansi' => '
					SELECT mpro."id" :mincols
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
                'mysql' => '
					SELECT mpro."id" :mincols
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY :order
					LIMIT :size OFFSET :start
				',
            ],
            'count' => [
                'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpro."id"
						FROM "mshop_product" mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY mpro."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
                'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpro."id"
						FROM "mshop_product" mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY mpro."id"
						LIMIT 10000 OFFSET 0
					) AS list
				',
            ],
            'cleanup' => [
                'ansi' => '
					DELETE FROM "mshop_index_price"
					WHERE "mtime" < ? AND "siteid" LIKE ?
				',
            ],
            'optimize' => [
                'mysql' => [
                    'OPTIMIZE TABLE "mshop_index_price"',
                ],
                'pgsql' => [],
                'sqlsrv' => [],
            ],
        ],
        'supplier' => [
            'delete' => [
                'ansi' => '
					DELETE FROM "mshop_index_supplier"
					WHERE :cond AND "siteid" LIKE ?
				',
            ],
            'insert' => [
                'ansi' => '
					INSERT INTO "mshop_index_supplier" (
						"prodid", "supid", "listtype", "pos",
						"latitude", "longitude", "mtime", "siteid"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?
					)
				',
                'pgsql' => '
					INSERT INTO "mshop_index_supplier" (
						"prodid", "supid", "listtype", "pos",
						"latitude", "longitude", "mtime", "siteid"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?
					)
					ON CONFLICT DO NOTHING
				',
            ],
            'search' => [
                'ansi' => '
					SELECT mpro."id" :mincols
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
                'mysql' => '
					SELECT mpro."id" :mincols
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY :order
					LIMIT :size OFFSET :start
				',
            ],
            'count' => [
                'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpro."id"
						FROM "mshop_product" mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY mpro."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
                'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpro."id"
						FROM "mshop_product" mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY mpro."id"
						LIMIT 10000 OFFSET 0
					) AS list
				',
            ],
            'cleanup' => [
                'ansi' => '
					DELETE FROM "mshop_index_supplier"
					WHERE "mtime" < ? AND "siteid" LIKE ?
				',
            ],
            'optimize' => [
                'mysql' => [
                    'OPTIMIZE TABLE "mshop_index_supplier"',
                ],
                'pgsql' => [],
                'sqlsrv' => [],
            ],
        ],
        'text' => [
            'delete' => [
                'ansi' => '
					DELETE FROM "mshop_index_text"
					WHERE :cond AND "siteid" LIKE ?
				',
            ],
            'insert' => [
                'ansi' => '
					INSERT INTO "mshop_index_text" (
						"prodid", "langid", "url", "name", "content", "mtime", "siteid"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?
					)
				',
                'pgsql' => '
					INSERT INTO "mshop_index_text" (
						"prodid", "langid", "url", "name", "content", "mtime", "siteid"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?
					)
					ON CONFLICT DO NOTHING
				',
            ],
            'search' => [
                'ansi' => '
					SELECT mpro."id" :mincols
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
                'mysql' => '
					SELECT mpro."id" :mincols
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY :order
					LIMIT :size OFFSET :start
				',
            ],
            'count' => [
                'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpro."id"
						FROM "mshop_product" mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY mpro."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
                'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpro."id"
						FROM "mshop_product" mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY mpro."id"
						LIMIT 10000 OFFSET 0
					) AS list
				',
            ],
            'cleanup' => [
                'ansi' => '
					DELETE FROM "mshop_index_text"
					WHERE "mtime" < ? AND "siteid" LIKE ?
				',
            ],
            'optimize' => [
                'mysql' => [
                    'OPTIMIZE TABLE "mshop_index_text"',
                ],
                'pgsql' => [],
                'sqlsrv' => [],
            ],
        ],
        'aggregate' => [
            'ansi' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val" :mincols
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY :cols, :val, mpro."id"
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				) AS list
				GROUP BY :keys
			',
            'mysql' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val" :mincols
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY :cols, :val, mpro."id"
					ORDER BY :order
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			',
        ],
        'search' => [
            'ansi' => '
				SELECT mpro."id" :mincols
				FROM "mshop_product" mpro
				:joins
				WHERE :cond
				GROUP BY mpro."id"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
            'mysql' => '
				SELECT mpro."id" :mincols
				FROM "mshop_product" mpro
				:joins
				WHERE :cond
				GROUP BY mpro."id"
				ORDER BY :order
				LIMIT :size OFFSET :start
			',
        ],
        'count' => [
            'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mpro."id"
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY mpro."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
            'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mpro."id"
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY mpro."id"
					LIMIT 10000 OFFSET 0
				) AS list
			',
        ],
        'optimize' => [
            'mysql' => [
                'ANALYZE TABLE "mshop_product"',
                'ANALYZE TABLE "mshop_product_list"',
            ],
            'pgsql' => [],
            'sqlsrv' => [],
        ],
        'domains' => [
            'attribute' => 'attribute',
            'catalog' => 'catalog',
            'price' => ['default'],
            'product' => ['default'],
            'supplier' => 'supplier',
            'supplier/address' => 'supplier/address',
            'text' => 'text',
        ],
        'submanagers' => [
            'attribute' => 'attribute',
            'supplier' => 'supplier',
            'catalog' => 'catalog',
            'price' => 'price',
            'text' => 'text',
        ],
    ],
];
