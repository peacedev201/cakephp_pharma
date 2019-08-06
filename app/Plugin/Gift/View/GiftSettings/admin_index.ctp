<?php
    __d('gift', 'Gift');
    __d('gift', 'Enable gift plugin');
    __d('gift', 'Items per page');
    __d('gift', 'Photo gift price in credits');
    __d('gift', 'Audio gift price in credits');
    __d('gift', 'Video gift price in credits');
    __d('gift', 'Path to FFMPEG');
    __d('gift', 'Popular items per page');
    __d('gift', 'Please enter the full path to your FFMPEG installation. Contact your hosting provider to get the path to FFMPEG. Note, FFMPEG is required for sending video-gifts');
    __d('gift', 'Can send gifts');
    __d('gift', 'Allow photo gifts');
    __d('gift', 'Allow audio gifts');
    __d('gift', 'Allow video gifts');
    $this->Html->addCrumb(__d('gift', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('gift', 'Settings'), array('controller' => 'gift_settings'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Gift'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Gift', __d('gift','Settings'));?>
<?php echo $this->element('admin/setting');?>