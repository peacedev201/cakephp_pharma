<?php 
    __d('business', 'Can view business');
    __d('business', 'Can create business');
    __d('business', 'Claimable Page Creators');
    __d('business', 'Business top category items');
    __d('business', 'Business feeds item per page');
    __d('business', 'Business follow item per page');
    __d('business', 'Business search item per page');
    __d('business', 'Business review per page');
    __d('business', 'Business search default distance');
    __d('business', 'Business photo per page');
    __d('business', 'Number of top business by location');
    __d('business', 'Number of popular location');
    __d('business', 'Featured price per day');
    __d('business', 'Business');
    __d('business', 'Auto approve new item');
    __d('business', 'Number of featured items');
    __d('business', 'Business People Check-in Items');
    __d('business', 'Business follower item per page');
    __d('business', 'Business Same Categories Items');
    __d('business', 'Recent Reviews Items');
    __d('business', 'Reviews of Day Items');
    __d('business', 'Auto approve business');
    __d('business', 'Auto approve sub page');
    __d('business', 'Business sub page per page');
    __d('business', 'Enable Business Hashtag');
    __d('business', 'By pass force login');
?>
<?php 
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->Html->addCrumb(__d('business',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('business',  'Business Settings'), array(
        'controller' => 'business_settings', 
        'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Business'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Business', __d('business',  'Business Settings'));?>
<?php echo $this->element('admin/setting');?>