<?php

if (!function_exists('GetGalleryItemsDimensions')) {
    /**
     * Mansory Loop Dimensions
     *
     */
    function GetGalleryItemsDimensions(
        int $key,
        array $dataBig = [2, 7, 17],
        int $width = 370,
        int $height = 464,
        int $bigWidth = 743,
        int $bigHeight = 931
    ): array {
        if (in_array($key + 1, $dataBig)) {
            $width = $bigWidth;
            $height = $bigHeight;
        }

        return ['width' => $width, 'height' => $height];
    }
}
