<?php

class GifCommentsController extends GifCommentAppController {

    public function admin_index() {
        
    }

    public function index() {
        
    }

    public function get_fcontent($url, $javascript_loop = 0, $timeout = 500) {
        $url = str_replace("&amp;", "&", urldecode(trim($url)));

        $cookie = tempnam("/tmp", "CURLCOOKIE");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        $content = curl_exec($ch);
        $response = curl_getinfo($ch);
        if ($content === false) {
            $content = curl_error($ch);
            echo stripslashes($content);
            die();
        }
        curl_close($ch);


        if ($response['http_code'] == 301 || $response['http_code'] == 302) {
            ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");

            if ($headers = get_headers($response['url'])) {
                foreach ($headers as $value) {
                    if (substr(strtolower($value), 0, 9) == "location:")
                        return get_url(trim(substr($value, 9, strlen($value))));
                }
            }
        }

        if (( preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value) ) && $javascript_loop < 5) {
            return get_url($value[1], $javascript_loop + 1);
        } else {
            return array($content, $response);
        }
    }

    public function upload_gif() {
        if ($this->request->data['gif_link']) {
            $link = $this->request->data['gif_link'];

            // check url path .
            $tmpLink = substr($link, 0, 24);
            $tmpLink2 = 'https://media.tenor.com';
            similar_text(trim($tmpLink), trim($tmpLink2), $percent);
            if ((int) $percent != 100) {
                $result['error'] = true;
                $result['message'] = __("Your gif image is not correct");
                echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
                exit();
            }


            $path = 'uploads' . '/' . 'tmp';
            $url = 'uploads/tmp/';

            /* Extract the filename */
            $filename = explode('/', $link);
            $lurl = $this->get_fcontent($link);

            /* Save file wherever you want */
            $result = array();
            if (file_put_contents($url . $filename[4] . '.gif', $lurl[0])) {
                $result['thumb'] = FULL_BASE_URL . $this->request->webroot . $url . $filename[4] . '.gif';
                $result['file'] = $path . '/' . $filename[4] . '.gif';
            } else {
                $result['error'] = true;
            }
            echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
//        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
//
//        App::import('Vendor', 'qqFileUploader');
//        $uploader = new qqFileUploader($allowedExtensions);
//
//        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
//        $result = $uploader->handleUpload($path);
//
//        if (!empty($result['success'])) {
//            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
//
//            $result['thumb'] = FULL_BASE_URL . $this->request->webroot . $url . $result['filename'];
//            $result['file'] = $path . DS . $result['filename'];
//        }
            // to pass data through iframe you will need to encode all html tags
            //echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
            die();
        }
    }

}
