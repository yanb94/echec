<?php

namespace App\Service;

use Imagick;

class GenerateResponsiveImage
{
    const IMAGE_XL = 1200;
    const IMAGE_XL_FOLDER = "XL";

    const IMAGE_L = 992;
    const IMAGE_L_FOLDER = "L";

    const IMAGE_M = 768;
    const IMAGE_M_FOLDER = "M";

    const IMAGE_S = 576;
    const IMAGE_S_FOLDER = "S";

    private string $imagePath;
    private string $imageResponsivePath;

    public function __construct()
    {
        $this->imagePath = __DIR__.'/../../assets/images';
        $this->imageResponsivePath = __DIR__."/../../assets/images_responsives";
    }

    public function generateResponsivesImages()
    {
        $imageList = array_diff(scandir($this->imagePath), array('.', '..'));

        foreach ($imageList as $image) {
            $this->createAndSaveNewImage($image, self::IMAGE_XL, self::IMAGE_XL_FOLDER);
            $this->createAndSaveNewImage($image, self::IMAGE_L, self::IMAGE_L_FOLDER);
            $this->createAndSaveNewImage($image, self::IMAGE_M, self::IMAGE_M_FOLDER);
            $this->createAndSaveNewImage($image, self::IMAGE_S, self::IMAGE_S_FOLDER);
        }
    }

    private function createAndSaveNewImage(string $image, int $dimension, string $folder): void
    {
        $imagick = new Imagick(realpath($this->imagePath."/".$image));
        $imagick->resizeImage($dimension, 0, Imagick::FILTER_UNDEFINED, 1);
        $imagick->writeImage($this->imageResponsivePath."/".$folder."/".$image);
    }
}
