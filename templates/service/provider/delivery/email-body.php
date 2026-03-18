<?php

declare(strict_types=1);

$enc = $this->encoder();

echo sprintf($this->translate('mshop', 'There are %1$s new orders available'), count($this->get('orderItems', [])));
