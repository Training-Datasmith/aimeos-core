<?php

declare(strict_types=1);

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */

return [
    'manager' => [
        'delete' => [
            'ansi' => '
				DELETE FROM "madmin_log"
				WHERE :cond AND "siteid" LIKE ?
			',
        ],
        'insert' => [
            'ansi' => '
				INSERT INTO "madmin_log" ( :names
					"facility", "timestamp", "priority", "message", "request", "siteid"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?
				)
			',
        ],
        'update' => [
            'ansi' => '
				UPDATE "madmin_log"
				SET :names
					"facility" = ?, "timestamp" = ?, "priority" = ?, "message" = ?, "request" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			',
        ],
        'search' => [
            'ansi' => '
				SELECT :columns
				FROM "madmin_log" malog
				:joins
				WHERE :cond
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
            'mysql' => '
				SELECT :columns
				FROM "madmin_log" malog
				:joins
				WHERE :cond
				ORDER BY :order
				LIMIT :size OFFSET :start
			',
        ],
        'count' => [
            'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM(
					SELECT malog."id"
					FROM "madmin_log" malog
					:joins
					WHERE :cond
					ORDER BY "id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
            'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM(
					SELECT malog."id"
					FROM "madmin_log" malog
					:joins
					WHERE :cond
					ORDER BY "id"
					LIMIT 10000 OFFSET 0
				) AS list
			',
        ],
        'newid' => [
            'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
            'mysql' => 'SELECT LAST_INSERT_ID()',
            'oracle' => 'SELECT madmin_log_seq.CURRVAL FROM DUAL',
            'pgsql' => 'SELECT lastval()',
            'sqlite' => 'SELECT last_insert_rowid()',
            'sqlsrv' => 'SELECT @@IDENTITY',
            'sqlanywhere' => 'SELECT @@IDENTITY',
        ],
    ],
];
