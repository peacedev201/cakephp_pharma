<?php
    $this->Html->addCrumb(__d('ads','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('ads','General Settings'), array('controller' => 'ads_settings'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Ads'));
    $this->end();
    __d('ads','How often the ad statistic report will send to advertisers');
    __d('ads','Weekly');
    __d('ads','Monthly');
    __d('ads','Paypal test');
    __d('ads','Enable ads plugin');
    __d('ads','Ad refresh time (Second)');
    __d('ads','Auto report will send');
    __d('ads','Paypal Email');
    __d('ads','Save Settings');
?>
<?php echo$this->Moo->renderMenu('Ads', __d('ads','General Settings'));?>
<?php echo $this->element('admin/setting');?>
