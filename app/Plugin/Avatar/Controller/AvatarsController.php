<?php 
class AvatarsController extends AvatarAppController{
    public $check_subscription = false;
    public $check_force_login = false;
    public function admin_index()
    {
    }
    public function index()
    {
        $this->autoRender = false;
        if($this->request->query('info'))
        {
            $info = $this->request->query('info');
            $info = base64_decode($info);
            $info = json_decode($info);
            App::import('Avatar.Lib','AvatarGenerator');
            $avatar = AvatarGenerator::render($info->name,$info->size);
            $this->response->type('jpg');
            $this->response->body(base64_decode($avatar));
        }
    }
    public function test()
    {
        $this->autoRender = false;
        if($this->request->query('info'))
        {
            $info = $this->request->query('info');
            $info = base64_decode($info);
            $info = json_decode($info);
            var_dump($info);
        }
    }
}