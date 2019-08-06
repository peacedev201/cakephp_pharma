<?php
App::uses('Controller', 'Controller');
class ChatGzipController extends Controller{
    public $components = array('Cookie');
    public function moochat($type='desktop'){
        error_reporting(0);
        $this->response->header(array(
            'Content-encoding' => 'gzip',
            'Content-Type' => 'application/x-javascript;charset=utf-8',
            'Pragma'=>'cache',
        ));
        $this->response->maxAge(31536000);
        $this->response->mustRevalidate(true);
        $this->response->cache('-1 minute', '+100 days');
        $this->response->expires('+100 days');
        $this->response->sharable(true, 31536000);
        $this->response->modified();
        //$this->response->statusCode(304);
        switch ($type){
            case 'mobi':
                $path = APP.WEBROOT_DIR.DS."chat".DS."js".DS."client".DS."mooChat-mobile.js.gz";
                break;
            default:
                $path = APP.WEBROOT_DIR.DS."chat".DS."js".DS."client".DS."mooChat.js.gz";
        }
        $file = new File($path);
        //$this->gzipHeader(); // Uncomment when release
        if($file->exists()){
            $content =  $file->read();
            $this->response->etag(md5($content));
            $this->response->body($content);
        }
        if ($this->response->checkNotModified($this->request)) {
            return $this->response;
        }
        return $this->response;
        die();
    }
    public function chunk($name=''){
        //error_reporting(0);  remove comment when it's ready  for merging
        $name = explode("?", $name);


        $path = APP.WEBROOT_DIR.DS."chat".DS."js".DS."client".DS.$name[0];
        $file = new File($path);
        $this->gzipHeader(); // Uncomment when release
        if($file->exists()){
            $content =  $file->read();
            $this->response->etag(md5($content));
            $this->response->body($content);
        }

        return $this->response;

        exit;
    }
    private function gzipHeader(){
        $this->response->header(array(
            'Content-encoding' => 'gzip',
            'Content-Type' => 'application/x-javascript;charset=utf-8',
            'Pragma'=>'cache',
        ));
        $this->response->maxAge(31536000);
        $this->response->mustRevalidate(true);
        $this->response->cache('-1 minute', '+100 days');
        $this->response->expires('+100 days');
        $this->response->sharable(true, 31536000);
        $this->response->modified();
    }
}