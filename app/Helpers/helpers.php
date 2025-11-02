<?php

if (!function_exists('getOrdinalSuffix')) {
    /**
     * Get ordinal suffix for a number (1st, 2nd, 3rd, 4th, etc.)
     *
     * @param int $number
     * @return string
     */
    function getOrdinalSuffix($number)
    {
        $suffixes = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return 'th';
        }
        
        return $suffixes[$number % 10];
    }
}