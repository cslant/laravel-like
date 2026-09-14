<?php

if (!function_exists('count_digital')) {
    /**
     * Get digital count.
     *
     * @param  int  $count
     *
     * @return int|string
     */
    function count_digital(int $count): int|string
    {
        if ($count < 1000) {
            return $count;
        }

        if ($count < 1000000) {
            $thousands = $count / 1000;

            if ($thousands >= 999.5) {
                return '1M';
            }

            return round($thousands, 1) . 'K';
        }

        return round($count / 1000000, 1) . 'M';
    }
}
