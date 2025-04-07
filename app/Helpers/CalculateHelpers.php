<?php

if (!function_exists('targetCalculate')) {
    function targetCalculate($totalAmount, $target): string
    {
        if ($target > 0) {
            return number_format((($totalAmount / $target) * 100), 2);
        }

        return number_format(0, 2);
    }
}

if (!function_exists('discounted_total')) {
    function discountedTotal($pricing)
    {
        if ($pricing->discount > 0) {
            if ($pricing->discount_type === 'percent') {
                $total = ceil($pricing->grand_total - ($pricing->discount / 100));

                return ceil($total + $pricing->shipping_cost);
            } else {
                $total = ceil($pricing->grand_total - $pricing->discount);

                return ceil($total + $pricing->shipping_cost);
            }
        } else {
            return ceil($pricing->grand_total + $pricing->shipping_cost);
        }
    }
}
