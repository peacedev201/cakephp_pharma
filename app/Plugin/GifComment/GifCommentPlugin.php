<?php 
App::uses('MooPlugin','Lib');
class GifCommentPlugin implements MooPlugin{
    public function install(){}
    public function uninstall(){}
    public function settingGuide(){}
    public function menu()
    {
        return array(
         __d('gif_comment', 'General')  => array('plugin' => 'gif_comment', 'controller' => 'gif_comment_settings', 'action' => 'admin_index'),
            //'Settings' => array('plugin' => 'gif_comment', 'controller' => 'gif_comment_settings', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}