<?php

declare(strict_types=1);

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2026
 */

return [
    [
        'label' => 'demo-voucher', 'provider' => 'Voucher', 'status' => 1,
        'datestart' => null, 'dateend' => null,
        'config' => [
            'voucher.productcode' => 'demo-rebate',
        ],
        'codes' => [],
    ],
    [
        'label' => 'demo-fixed', 'provider' => 'FixedRebate,Basket', 'status' => 1,
        'datestart' => null, 'dateend' => null,
        'config' => [
            'fixedrebate.productcode' => 'demo-rebate',
            'fixedrebate.rebate' => [ 'EUR' => 125.00, 'USD' => 150.00 ],
            'basket.total-value-min' => [ 'EUR' => 125.00, 'USD' => 150.00 ],
        ],
        'codes' => [
            [
                'code' => 'fixed', 'count' => 1000,
                'datestart' => null, 'dateend' => null,
            ],
        ],
    ],
    [
        'label' => 'demo-percent', 'provider' => 'PercentRebate', 'status' => 1,
        'datestart' => null, 'dateend' => null,
        'config' => [
            'percentrebate.productcode' => 'demo-rebate',
            'percentrebate.rebate' => '10',
        ],
        'codes' => [
            [
                'code' => 'percent', 'count' => 1000,
                'datestart' => null, 'dateend' => null,
            ],
        ],
    ],
];
