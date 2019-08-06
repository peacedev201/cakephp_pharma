<?php 
/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('MooPlugin','Lib');
class GroupPlugin implements MooPlugin{
    public function install(){}
    public function uninstall(){}
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __('General') => array('plugin' => 'group', 'controller' => 'group_plugins', 'action' => 'admin_index'),
            __('Manage') => array('plugin' => 'group', 'controller' => 'group_manage', 'action' => 'admin_index'),
            __('Criteria') => array('plugin' => 'group', 'controller' => 'group_criteria', 'action' => 'admin_index'),
            __('Settings') => array('plugin' => 'group', 'controller' => 'group_settings', 'action' => 'admin_index'),
            __('Categories') => array('plugin' => 'group', 'controller' => 'group_categories', 'action' => 'admin_index'),
        );
    }
}