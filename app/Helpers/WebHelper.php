<?php

if (!function_exists('getPhotoGalleryDimensions')) {
    /**
     * PV Photo Loop Dimensions
     *
     * @param
     * @return
     */
    function getPhotoGalleryDimensions(int $key, int $width, int $height, int $bigWidth, int $bigHeight): array
    {
        $width = 370;
        $height = 464;

        if (in_array($key + 1, [2, 7, 17])) {
            $width = 743;
            $height = 931;
        }

        return ['width' => $width, 'height' => $height];
    }
}
