<?php

namespace App\Helpers;


class OrderHelper
{

    /**
     * @param int $status
     *
     * @return bool
     */
    public static function isCanceled(int $status): bool
    {
        $canceled = [5, 7];

        foreach ($canceled as $value) {
            if ($value == $status) {
                return true;
            }
        }
        /*if (is_array(config('settings.order.canceled_status'))) {
            foreach (config('settings.order.canceled_status') as $value) {
                if ($value == $status) {
                    return true;
                }
            }
        } else {
            if (config('settings.order.canceled_status') == $status) {
                return true;
            }
        }*/

        return false;
    }
}
