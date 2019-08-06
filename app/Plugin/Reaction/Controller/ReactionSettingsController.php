<?php 
class ReactionSettingsController extends ReactionAppController{
    public $components = array('QuickSettings');

    public function admin_index($id=null)
    {
        $this->QuickSettings->run($this, array("Reaction"), $id);
        if (CakeSession::check('Message.flash')) {
            $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
            $menu = $menuModel->findByUrl('/reaction');
            if ($menu)
            {
                $menuModel->id = $menu['CoreMenuItem']['id'];
                $menuModel->save(array('is_active'=>Configure::read('Reaction.reaction_enabled')));
            }
            Cache::clearGroup('menu', 'menu');
        }
        $this->set('title_for_layout', __d('reaction', 'Reaction Settings'));
        //$this->_fixed_table();
    }

    private function _fixed_table(){
        if( !Configure::read('Reaction.reaction_enabled') ){
            $db = ConnectionManager::getDataSource("default");
            $mSetting = MooCore::getInstance()->getModel('Setting');
            $table_prefix = $mSetting->tablePrefix;
            $db->query("UPDATE `".$table_prefix."reactions` SET `is_update`= 1");
            $db->query("UPDATE `".$table_prefix."likes` SET `reaction`= 0 WHERE `id` IN ( SELECT * FROM (SELECT `id` FROM `".$table_prefix."likes` WHERE `thumb_up` = 0) AS L)");
            $db->query("UPDATE `".$table_prefix."likes` SET `reaction`= 1 WHERE `id` IN ( SELECT * FROM (SELECT `id` FROM `".$table_prefix."likes` WHERE `thumb_up` = 1 AND `reaction` = 0) AS L);");
        }
    }
}