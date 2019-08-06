<?php

namespace YoHang88\LetterAvatar;

use Intervention\Image\Gd\Font;
use Intervention\Image\Gd\Shapes\CircleShape;
use Intervention\Image\ImageManager;

class LetterAvatar
{
    /**
     * Image Type PNG
     */
    const MIME_TYPE_PNG = 'image/png';

    /**
     * Image Type JPEG
     */
    const MIME_TYPE_JPEG = 'image/jpeg';

    /**
     * @var string
     */
    private $name;


    /**
     * @var string
     */
    private $nameInitials;


    /**
     * @var string
     */
    private $shape;


    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $specialty;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * LetterAvatar constructor.
     * @param string $name
     * @param string $shape
     * @param int    $size
     */
    public function __construct($name, $shape = 'circle', $size = '48', $specialty = 0)
    {
        $this->setName($name);
        $this->setImageManager(new ImageManager());
        $this->setShape($shape);
        $this->setSize($size);
        $this->setSpecialty($specialty);
    }

    /**
     * @param string $name
     */
    private function setName($name)
    {
        $this->name = $name;
    }


    /**
     * @param ImageManager $imageManager
     */
    private function setImageManager(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * @param string $shape
     */
    private function setShape($shape)
    {
        $this->shape = $shape;
    }


    /**
     * @param int $size
     */
    private function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @param int $size
     */
    private function setSpecialty($specialty)
    {
        $this->specialty = $specialty;
    }


    /**
     * @return \Intervention\Image\Image
     */
    private function generate()
    {
        $isCircle = $this->shape === 'circle';
        $is_Korean =  $this->isKorean($this->name);
        if($is_Korean)
            $this->nameInitials = $name = $this->genKoreanName($this->name);
        else
            $this->nameInitials = $name = $this->getInitials($this->name);
        $color = $this->stringToColor($this->name);

        $canvas = $this->imageManager->canvas(480, 480, $isCircle ? null : $color);

        if ($isCircle) {
            $canvas->circle(480, 240, 240, function (CircleShape $draw) use ($color) {
                $draw->background($color);
            });

        }

        $specialty = intval($this->specialty);
        $add_img = 'pharmalink.png';
        if($specialty == 0){
            $add_img = 'pharmalink.png';
        }else if($specialty == 1){//SPECIALTY_PHARMACIST
            $add_img = 'pharmacist.png';
        }else if($specialty == 2){//SPECIALTY_STUDENT
            $add_img = 'student.png';
        }else if($specialty == 4){//SPECIALTY_SALE
            $add_img = 'sale.png';
        }
        if($add_img){
            $canvas->insert(APP.DS."webroot".DS."avatar".DS."img".DS.$add_img, 'top-center',0,22);
        }

        //$this->nameInitials = "a\n".$this->nameInitials;
        $canvas->text($this->nameInitials, 240, 240, function (Font $font) use ($is_Korean, $name) {
            $font->file(__DIR__ . '/fonts/arial-bold.ttf');
            if($is_Korean && mb_strlen($name) == 3)
                $font->size(150);
            elseif($is_Korean && mb_strlen($name) == 4)
                $font->size(120);
            else
                $font->size(220);
            $font->color('#fafafa');
            $font->valign('center');
            $font->align('center');
        });

        return $canvas->resize($this->size, $this->size);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getInitials($name)
    {
        $nameParts = $this->break_name($name);

        if(!$nameParts) {
            return '';
        }
        $secondLetter = isset($nameParts[1]) ? $this->getFirstLetter($nameParts[1]) : '';

        return $this->getFirstLetter($nameParts[0]) . $secondLetter;

    }

    /**
     * @param string $word
     * @return string
     */
    private function getFirstLetter($word)
    {
        return mb_strtoupper(trim(mb_substr($word, 0, 1, 'UTF-8')));
    }

    /**
     * Save the generated Letter-Avatar as a file
     *
     * @param        $path
     * @param string $mimetype
     * @param int    $quality
     * @return bool
     */
    public function saveAs($path, $mimetype = 'image/png', $quality = 90)
    {
        $allowedMimeTypes = [
            'image/png',
            'image/jpeg'
        ];

        if (empty($path) || empty($mimetype) || !\in_array($mimetype, $allowedMimeTypes, true)) {
            return false;
        }

        return \is_int(@file_put_contents($path, $this->generate()->encode($mimetype, $quality)));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->generate()->encode('data-url');
    }

    /**
     * Explodes Name into an array.
     * The function will check if a part is , or blank
     *
     * @param string $name Name to be broken up
     * @return array Name broken up to an array
     */
    private function break_name($name)
    {
        $words = \explode(' ', $name);
        $words = array_filter($words, function($word) {
            return $word!=='' && $word !== ',';
        });
        return array_values($words);
    }

    /**
     * @param string $string
     * @return string
     */
    private function stringToColor($string)
    {
        // random color
        $rgb = substr(dechex(crc32($string)), 0, 6);
        // make it darker
        $darker = 2;
        list($R16, $G16, $B16) = str_split($rgb, 2);
        $R = sprintf('%02X', floor(hexdec($R16) / $darker));
        $G = sprintf('%02X', floor(hexdec($G16) / $darker));
        $B = sprintf('%02X', floor(hexdec($B16) / $darker));
        return '#' . $R . $G . $B;
    }

    private function isKorean($string)
    {
        return preg_match('/[\x{3130}-\x{318F}\x{AC00}-\x{D7AF}]/u', $string);
    }

    public function genKoreanName($string)
    {
        $length = mb_strlen($string,'UTF-8');
        if($length > 4) $length = 4;
        return mb_strtoupper(trim(mb_substr($string, 0, $length, 'UTF-8')));
    }

}
