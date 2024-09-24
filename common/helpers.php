<?php

if (!function_exists('count_digital')) {
    /**
     * Get digital count.
     *
     * @param $count
     *
     * @return string
     */
    function count_digital($count): string
    {
        if ($count < 1000) {
            return $count;
        }

        if ($count < 1000000) {
            return round($count / 1000, 1) . 'K';
        }

        return round($count / 1000000, 1) . 'M';
    }
}
