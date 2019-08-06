<?php
/**
 * Created by PhpStorm.
 * User: QUOC-PC
 * Date: 10/08/2018
 * Time: 10:38 SA
 */

require_once APP.DS."Plugin".DS."Avatar".DS."Lib".DS.'LetterAvatar'.DS.'vendor'.DS.'autoload.php';
use YoHang88\LetterAvatar\LetterAvatar;

class AvatarGenerator
{
    public static function render($name = null,$size = 50)
    {
        $shape = Configure::read('Avatar.avatar_shape')? : 'circle';
        $avatar = new LetterAvatar($name, $shape, $size);
        return str_replace('data:image/png;base64,','',$avatar);
    }

    public static function save_avatar($name = null,$size = 50)
    {
        try {
            $shape = Configure::read('Avatar.avatar_shape')? : 'circle';
            $avatar = new LetterAvatar($name, $shape, $size);
            $avatar->saveAs(APP.DS."webroot".DS."avatar".DS. base64_encode($avatar->getInitials($name).$size.$shape).".png",
                LetterAvatar::MIME_TYPE_PNG);
            return true;
        }
        catch(Exception $e) {
            return false;
        }
    }

    public static function save_avatar_tmp($name = null,$size = 50, $specialty = 0)
    {
        try {
            $shape = 'square';
            $tmp_name = md5(uniqid());
            $avatar = new LetterAvatar($name, $shape, $size, $specialty);
            $avatar->saveAs(APP.DS."webroot".DS."uploads".DS. "tmp" .DS. $tmp_name.".png",
                LetterAvatar::MIME_TYPE_PNG);
            return $tmp_name;
        }
        catch(Exception $e) {
            return false;
        }
    }
}