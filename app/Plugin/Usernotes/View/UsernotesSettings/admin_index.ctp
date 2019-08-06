<?php
    $this->Html->addCrumb(__d('usernotes','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('usernotes','Manage Settings'), array('controller' => 'usernotes_settings'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Usernotes'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Usernotes', __d('usernotes','Settings'));?>
<?php echo $this->element('admin/setting');?>
