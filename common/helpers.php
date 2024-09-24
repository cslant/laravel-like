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
            return round($count / 1000, 1) . 'K';
        }

        return round($count / 1000000, 1) . 'M';
    }
}
