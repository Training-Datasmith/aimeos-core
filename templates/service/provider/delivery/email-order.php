<?php

declare (strict_types=1);
$csv = function (string $type, string $id, array $data) {
    foreach ($data as $pos => $entry) {
        // ltrim to invalidate Excel macros
        $data[$pos] = '"' . str_replace('"', '""', ltrim(json_encode($entry), '@=+-')) . '"';
    }
    return '"' . $type . '";"' . $id . '";' . join(';', $data) . PHP_EOL;
};
foreach ($this->get('orderItems', []) as $order_item) {
    $data = ['order.ordernumber' => $order_item->get_order_number()] + $order_item->to_array();
    echo $csv('invoice', $order_item->get_id(), $data);
    foreach ($order_item->get_addresses()->krsort() as $type => $addresses) {
        foreach ($addresses as $address) {
            echo $csv('address', $order_item->get_id(), $address->to_array());
        }
    }
    foreach ($order_item->get_products() as $product) {
        $list = $product->to_array();
        foreach ($product->get_attribute_items() as $attr_item) {
            foreach ($attr_item->to_array(true) as $key => $value) {
                if (isset($list[$key])) {
                    $list[$key] .= "\n" . $value;
                } else {
                    $list[$key] = $value;
                }
            }
        }
        echo $csv('product', $order_item->get_id(), $list);
    }
    foreach ($order_item->get_services()->krsort() as $type => $services) {
        foreach ($services as $service) {
            $list = $service->to_array();
            foreach ($service->get_attribute_items() as $attr_item) {
                foreach ($attr_item->to_array(true) as $key => $value) {
                    if (isset($list[$key])) {
                        $list[$key] .= "\n" . $value;
                    } else {
                        $list[$key] = $value;
                    }
                }
            }
            echo $csv('service', $order_item->get_id(), $list);
        }
    }
    echo PHP_EOL;
}