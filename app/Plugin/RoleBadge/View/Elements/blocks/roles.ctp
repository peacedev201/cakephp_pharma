<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php 
$oModelRole = MooCore::getInstance()->getModel('Role');
$aRoles = $oModelRole->find('list', array('fields' => array('Role.id', 'Role.name')));
?>

<?php 
echo $this->Form->input('role_id',array(
    'type' => 'select',
    'name'=>'role_id',
    'options' => $aRoles,
    'before' => '',
    'separator' => '',
    'after' => '</div>',
    'class' => 'form-control',
    'between' => '<div class="col-lg-9">',
    'div' => array("class" => 'form-group'),
    'label' => array('text' => __d('role_badge', 'Role'), 'class' => 'col-lg-3 control-label'),
));
?>